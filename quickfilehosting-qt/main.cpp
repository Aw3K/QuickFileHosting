#include "accountswindow.h"
#include "mainwindow.h"
#include "uploadedfiles.h"
#include "userdata.h"
#include <QApplication>
#include <QSqlQuery>

int main(int argc, char *argv[])
{
    QSqlDatabase db;
    db = QSqlDatabase::addDatabase("QMYSQL", "db");
    db.setHostName("");
    db.setDatabaseName("");
    db.setUserName("");
    db.setPassword("");
    db.setPort(3306);

    QPalette darkPalette;
    QColor backgroundColor("#191d21");
    QColor textColor("#32cd32");
    QColor buttonColor("#2a2e33");
    QColor highlightColor("#388e3c");
    QColor alternateColor("#23272b");
    QColor disabledTextColor("#4f545a");

    darkPalette.setColor(QPalette::Window, backgroundColor);
    darkPalette.setColor(QPalette::WindowText, textColor);
    darkPalette.setColor(QPalette::Base, backgroundColor.darker(120));
    darkPalette.setColor(QPalette::AlternateBase, alternateColor);
    darkPalette.setColor(QPalette::ToolTipBase, backgroundColor);
    darkPalette.setColor(QPalette::ToolTipText, textColor);
    darkPalette.setColor(QPalette::Text, textColor);
    darkPalette.setColor(QPalette::Button, buttonColor);
    darkPalette.setColor(QPalette::ButtonText, textColor);
    darkPalette.setColor(QPalette::BrightText, "#ff5555");
    darkPalette.setColor(QPalette::Highlight, highlightColor);
    darkPalette.setColor(QPalette::HighlightedText, QColor(255,255,255));
    darkPalette.setColor(QPalette::Disabled, QPalette::Text, disabledTextColor);

    QApplication a(argc, argv);
    a.setPalette(darkPalette);
    UserData *currentUser = new UserData(0, "anonymous");
    uploadedFiles *f = new uploadedFiles(nullptr, currentUser);
    MainWindow w(nullptr, f, nullptr, currentUser);
    AccountsWindow *acc = new AccountsWindow(&w, currentUser);
    w.acc = acc;
    w.show();
    return a.exec();
}
