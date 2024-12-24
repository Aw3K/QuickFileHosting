#ifndef FILEUPLOADER_H
#define FILEUPLOADER_H

#include <QObject>
#include <QNetworkAccessManager>
#include <QFile>
#include "uploadedfiles.h"
#include "userdata.h"

class FileUploader : public QObject {
    Q_OBJECT

public:
    explicit FileUploader(QObject *parent = nullptr, uploadedFiles *list = nullptr, UserData *currentUser = nullptr);
    void uploadFile(const QString &filePath, const QUrl &url, UserData *user);
    QFile *file;
    bool isUploading;

private slots:
    void onFinished(QNetworkReply *reply);
    void uploadProgress(qint64 bytesSent, qint64 bytesTotal);

private:
    QNetworkAccessManager *manager;
    uploadedFiles *list;
    UserData *currentUser;
};

#endif // FILEUPLOADER_H
