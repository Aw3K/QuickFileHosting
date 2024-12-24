#include "FileUploader.h"
#include <QFile>
#include <QHttpMultiPart>
#include <QNetworkReply>
#include <QJsonDocument>
#include <QJsonObject>
#include <QTextCodec>
#include "mainwindow.h"
#include "qfileinfo.h"
#include "uploadedfiles.h"

FileUploader::FileUploader(QObject *parent, uploadedFiles *list, UserData *currentUser)
    : QObject(parent) {
    this->list = list;
    this->currentUser = currentUser;
    manager = new QNetworkAccessManager(this);
    connect(manager, SIGNAL(finished(QNetworkReply*)), this, SLOT(onFinished(QNetworkReply*)));
}

void FileUploader::uploadFile(const QString &filePath, const QUrl &url, UserData *user) {
    if (this->isUploading) return;
    file = new QFile(filePath);
    if (!file->open(QIODevice::ReadOnly)) {
        qDebug() << "Could not open file for reading";
        delete file;
        return;
    }
    QHttpMultiPart *multiPart = new QHttpMultiPart(QHttpMultiPart::FormDataType);

    QHttpPart filePart;
    filePart.setHeader(QNetworkRequest::ContentDispositionHeader, QVariant("form-data; name=\"file\"; filename=\"" + file->fileName() + "\""));
    filePart.setBodyDevice(file);
    file->setParent(multiPart);

    multiPart->append(filePart);

    QNetworkRequest request(url);
    if (user->id > 0 && user->hash.length() > 1){
        request.setRawHeader("Authorization", user->hash.toUtf8());
    }

    QNetworkReply *reply = manager->post(request, multiPart);
    connect(reply, &QNetworkReply::uploadProgress, this, &FileUploader::uploadProgress);
    multiPart->setParent(reply);
}

void FileUploader::onFinished(QNetworkReply *reply) {
    UserData *user = nullptr;
    MainWindow* pMainWindow = qobject_cast<MainWindow*>(parent());
    user = pMainWindow->currentUser;
    int owner = 0;

    if (reply->error() != QNetworkReply::NoError) {
        pMainWindow->setUploadInfo("ERROR", "FILE_UPLOAD_FAILED", reply->errorString());
    } else {
        QJsonDocument document = QJsonDocument::fromJson(reply->readAll());
        QJsonObject responseObject = document.object();
        QFileInfo fileInfo(file->fileName());
        QString filename(fileInfo.fileName());
        if (responseObject["status"] == "SUCCESS" && (responseObject["code"] == "FILE_UPLOADED_NOUSER" || responseObject["code"] == "FILE_UPLOADED_USER")){
            if (user != nullptr && user->id > 0) owner = user->id;
            list->append(filename, responseObject["token"].toString(), owner, file->size(), QDate::currentDate().addMonths(1).toString("yyyy-MM-dd"),"false", false);

            if (pMainWindow){
                pMainWindow->reloadUploadedList();
                pMainWindow->setUploadInfo(responseObject["status"].toString(), responseObject["code"].toString(), responseObject["token"].toString());
            }
        }
    }
    reply->deleteLater();
    file->deleteLater();
    if (owner < 1) list->saveListToFile();
    pMainWindow->setUploadStatus(0,0);
    this->isUploading = false;
}

void FileUploader::uploadProgress(qint64 bytesSent, qint64 bytesTotal)
{
    if (bytesSent == 0 && bytesTotal == 0) this->isUploading = false;
    else {
        this->isUploading = true;
        MainWindow* pMainWindow = qobject_cast<MainWindow*>(parent());
        pMainWindow ->setUploadStatus(bytesSent, bytesTotal);
    }
}
