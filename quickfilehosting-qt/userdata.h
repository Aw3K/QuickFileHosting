#ifndef USERDATA_H
#define USERDATA_H

#include "qsqldatabase.h"
#include <QString>

class UserData
{
public:
    UserData(int id, QString username);
    int id;
    QString username;
    QString hash;
    long hashValidUntill;
    bool loadNewHash();
};

#endif // USERDATA_H
