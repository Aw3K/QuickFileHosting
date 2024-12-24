#include "userdata.h"
#include "qdatetime.h"
#include "qdebug.h"
#include "qsqldatabase.h"
#include "qsqlquery.h"
#include "quuid.h"

UserData::UserData(int id, QString username) {
    this->id = id;
    this->username = username;
}

bool UserData::loadNewHash(){
    if (this->id == 0) return false;
    QUuid uid = QUuid::createUuid();
    if (!QSqlDatabase::database("db").isOpen()) QSqlDatabase::database("db").open();
    QSqlQuery querry (QSqlDatabase::database("db"));
    querry.prepare("UPDATE `users` SET `hash`=? WHERE `id`=?;");
    querry.bindValue(0, uid.toString(QUuid::WithoutBraces));
    querry.bindValue(1, this->id);
    querry.exec();
    if (querry.numRowsAffected() == 1) {
        this->hash = uid.toString(QUuid::WithoutBraces);
        this->hashValidUntill = QDateTime::currentSecsSinceEpoch()+86400;
        return true;
    }
    return false;
}
