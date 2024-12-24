#ifndef UPLOADEDFILES_H
#define UPLOADEDFILES_H

#include "userdata.h"
#include <QObject>

struct uploadFileData{
    QString name;
    QString token;
    int owner;
    qint64 size;
    QString expires;
    QString remove;
    bool isPassLocked;
};

class uploadedFiles : public QObject
{
    Q_OBJECT

public:
    explicit uploadedFiles(QObject *parent = nullptr, UserData *currentUser = nullptr);
    QList<uploadFileData> uploadFileDataList;
    UserData *currentUser;
    void append(QString name, QString token, int owner, qint64 size, QString expires, QString remove, bool isPassLocked);
    void saveListToFile();
    void readListFromFile();
    void readListFromAccount();
    bool setRemoveStatus(int position, QString status);
    bool setPassword(int position, QString pass);

signals:
};

#endif // UPLOADEDFILES_H
