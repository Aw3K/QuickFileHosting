#ifndef ACCOUNTSWINDOW_H
#define ACCOUNTSWINDOW_H

#include <QMainWindow>
#include "userdata.h"

namespace Ui {
class AccountsWindow;
}

class AccountsWindow : public QMainWindow
{
    Q_OBJECT

public:
    explicit AccountsWindow(QWidget *parent = nullptr, UserData *currentUser = nullptr);
    ~AccountsWindow();
    int getLogged();
    void dbInit();
    void clearInputs();

public slots:
    void checkIfUserLogged();

private slots:
    void on_actionLogin_triggered();
    void on_actionRegister_triggered();
    void on_actionMore_Actions_triggered();
    void on_loginButton_clicked();
    void on_registerButton_4_clicked();
    void on_logoutButton_clicked();
    void checkIfValidated();

private:
    Ui::AccountsWindow *ui;
    QString generateMd5(QString pass);
    void setDatabaseInfo(QString info);
    UserData *currentUser;
    void setLoggedUser(int id, QString username, bool saveLocally);
    void sendRequestForAccountConfirm(QString email, QString hash);
};

#endif // ACCOUNTSWINDOW_H
