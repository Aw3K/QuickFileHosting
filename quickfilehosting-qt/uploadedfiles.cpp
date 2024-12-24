#include "uploadedfiles.h"
#include "qcryptographichash.h"
#include "qnetworkaccessmanager.h"
#include "qsqlquery.h"
#include <QFile>
#include <QStandardPaths>
#include <QUrlQuery>

uploadedFiles::uploadedFiles(QObject *parent, UserData *currentUser)
    : QObject{parent}
{
    uploadFileDataList.clear();
    this->currentUser = currentUser;
}

void uploadedFiles::append(QString name, QString token, int owner, qint64 size, QString expires, QString remove, bool isPassLocked){
    uploadFileData tmp;
    tmp.name = name;
    tmp.token = token;
    tmp.owner = owner;
    tmp.size = size;
    tmp.expires = expires;
    tmp.remove = remove;
    tmp.isPassLocked = isPassLocked;
    uploadFileDataList.append(tmp);
}

void uploadedFiles::saveListToFile() {
    QFile file(QStandardPaths::writableLocation(QStandardPaths::AppLocalDataLocation) + "/localuploads.ini");
    if (file.open(QIODevice::WriteOnly)) {
        QDataStream out(&file);
        QList<QString> fileTokens;
        foreach(uploadFileData fileData, uploadFileDataList){
            fileTokens.append(fileData.token);
        }
        out << fileTokens;
        file.close();
    }
}

void uploadedFiles::readListFromAccount() {
    if (currentUser == nullptr || currentUser->id < 1) return;
    uploadFileDataList.clear();
    if (!QSqlDatabase::database("db").isOpen()) QSqlDatabase::database("db").open();
    QSqlQuery querry (QSqlDatabase::database("db"));
    QString sql = QString("SELECT * FROM `files` WHERE `owner` = ?;");
    querry.prepare(sql);
    querry.bindValue(0, currentUser->id);
    querry.exec();
    while(querry.next()){
        if(querry.value("remove").toString() != "DONE") this->append(querry.value("fname").toString(),querry.value("token").toString(),querry.value("owner").toInt(),querry.value("size").toLongLong(),querry.value("expired").toString(), querry.value("remove").toString(), ((querry.value("pass").toString().isEmpty()) ? false : true));
    }
}

void uploadedFiles::readListFromFile() {
    QFile file(QStandardPaths::writableLocation(QStandardPaths::AppLocalDataLocation) + "/localuploads.ini");
    if (file.open(QIODevice::ReadOnly)) {
        QDataStream in(&file);
        QList<QString> fileTokens;
        in >> fileTokens;
        file.close();
        if (!QSqlDatabase::database("db").isOpen()) QSqlDatabase::database("db").open();
        QSqlQuery querry (QSqlDatabase::database("db"));
        QString sql = QString("SELECT * FROM `files` WHERE `token` IN (%1)").arg(QList<QString>(fileTokens.size(), "?").join(", "));
        querry.prepare(sql);
        for(int i = 0; i<fileTokens.size(); i++){
            querry.bindValue(i, fileTokens.at(i));
        }
        if(querry.exec()) uploadFileDataList.clear();
        while(querry.next()){
            if(querry.value("remove").toString() != "DONE") this->append(querry.value("fname").toString(),querry.value("token").toString(),querry.value("owner").toInt(),querry.value("size").toLongLong(),querry.value("expired").toString(), querry.value("remove").toString(), ((querry.value("pass").toString().isEmpty()) ? false : true));
        }
    }
}

bool uploadedFiles::setRemoveStatus(int position, QString status){
    if (uploadFileDataList[position].owner < 1 || currentUser->id != uploadFileDataList[position].owner) return false;
    if (!QSqlDatabase::database("db").isOpen()) QSqlDatabase::database("db").open();
    QSqlQuery querry (QSqlDatabase::database("db"));
    querry.prepare("UPDATE `files` SET `remove`=?,`locked`=? WHERE `token` = ? AND `owner` = ? AND `remove` != 'DONE';");
    querry.bindValue(0, status);
    querry.bindValue(1, status);
    querry.bindValue(2, uploadFileDataList[position].token);
    querry.bindValue(3, uploadFileDataList[position].owner);
    return querry.exec();
}

bool uploadedFiles::setPassword(int position, QString pass){
    if (uploadFileDataList[position].owner < 1 || currentUser->id != uploadFileDataList[position].owner) return false;
    if (!QSqlDatabase::database("db").isOpen()) QSqlDatabase::database("db").open();
    QSqlQuery querry (QSqlDatabase::database("db"));
    querry.prepare("UPDATE `files` SET `pass`=? WHERE `token` = ? AND `owner` = ? AND `remove` != 'DONE';");
    pass = (pass.isEmpty()) ? pass : QString(QCryptographicHash::hash(pass.toStdString(), QCryptographicHash::Md5).toHex());
    querry.bindValue(0, pass);
    querry.bindValue(1, uploadFileDataList[position].token);
    querry.bindValue(2, uploadFileDataList[position].owner);
    return querry.exec();
}
