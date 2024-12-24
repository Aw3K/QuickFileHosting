#include "accountswindow.h"
#include "fileuploader.h"
#include "mainwindow.h"
#include "qdatetime.h"
#include "qdir.h"
#include "qjsondocument.h"
#include "qjsonobject.h"
#include "qregularexpression.h"
#include "qtimer.h"
#include "quuid.h"
#include "qvalidator.h"
#include "ui_accountswindow.h"
#include "userdata.h"
#include <QCryptographicHash>
#include <QSqlQuery>
#include <QSqlError>
#include <QDesktopServices>
#include <QUrlQuery>
#include <QStandardPaths>
#include <sodium.h>

AccountsWindow::AccountsWindow(QWidget *parent, UserData* currentUser)
    : QMainWindow(parent)
    , ui(new Ui::AccountsWindow)
{
    ui->setupUi(this);
    this->setParent(parent);
    this->currentUser = currentUser;
    ui->password->setEchoMode(QLineEdit::Password);
    ui->repeatPasswordLineEdit->setEchoMode(QLineEdit::Password);
    ui->password_4->setEchoMode(QLineEdit::Password);
    this->setFixedSize(QSize(430, 300));
    QRegularExpression rx("\\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,5}\\b", QRegularExpression::CaseInsensitiveOption);
    ui->emailLineEdit->setValidator(new QRegularExpressionValidator(rx, this));
    connect(ui->emailLineEdit, &QLineEdit::textChanged, this, &AccountsWindow::checkIfValidated);
}

AccountsWindow::~AccountsWindow()
{
    delete ui;
}

int AccountsWindow::getLogged()
{
    if(!(currentUser->username).isEmpty() && (currentUser->id) > 0) return currentUser->id;
    else return 0;
}

void AccountsWindow::setDatabaseInfo(QString info)
{
    ui->databaseLabel->setText(info);
}

void AccountsWindow::checkIfUserLogged()
{
    if((currentUser->id) == 0) this->ui->accInfoLabel->setText("Currently You are using application as guest.");
    else if((currentUser->id) == -1) this->ui->accInfoLabel->setText("There were an error with account, try again.");
    else {
        this->ui->accInfoLabel->setText("Logged in as: " + currentUser->username);
        this->ui->stackedWidget->setCurrentIndex(2);
        this->ui->actionLogin->setDisabled(true);
        this->ui->actionRegister->setDisabled(true);
    }
}

void AccountsWindow::setLoggedUser(int id, QString username, bool saveLocally)
{
    currentUser->id = id;
    currentUser->username = username;
    if (!currentUser->loadNewHash()) currentUser->id = -1;
    else if (saveLocally) {
        QDir localAppData(QStandardPaths::writableLocation(QStandardPaths::AppLocalDataLocation));
        if (!localAppData.exists()) localAppData.mkpath(localAppData.path());
        QFile token(QStandardPaths::writableLocation(QStandardPaths::AppLocalDataLocation) + "/token.ini");
        if (token.open(QIODevice::WriteOnly) && token.write(username.toUtf8() + ";" + (currentUser->hash).toUtf8())){
        } else if (auto *parent = qobject_cast<MainWindow *>(this->parent())) {
            parent->setAccountInfo("LOCAL_DATA_CREATE_ERROR", "");
        }
    }
    this->close();
    QTimer::singleShot( 500, this->parent(), SLOT(loadLoggedUser()));
}

void AccountsWindow::sendRequestForAccountConfirm(QString email, QString hash)
{
    QUrl actionUrl = qobject_cast<MainWindow*>(this->parent())->actionsUrl;
    if (actionUrl.isValid()){
        QNetworkAccessManager *manager = new QNetworkAccessManager();
        QNetworkRequest request(actionUrl);
        request.setHeader(QNetworkRequest::ContentTypeHeader, "application/x-www-form-urlencoded");

        QUrlQuery postData;
        postData.addQueryItem("mode", "accountActivation");
        postData.addQueryItem("email", email);
        postData.addQueryItem("hash", hash);

        QNetworkReply *reply = manager->post(request, postData.toString(QUrl::FullyEncoded).toUtf8());

        QObject::connect(reply, &QNetworkReply::finished, this, [reply, this]() {
            if (reply->error() == QNetworkReply::NoError) {
                QJsonDocument document = QJsonDocument::fromJson(reply->readAll());
                QJsonObject responseObject = document.object();
                if (responseObject["status"] == "SUCCESS"){
                    ui->accInfoLabel->setText("Account created, check email for activation link.");
                    ui->stackedWidget->setCurrentIndex(0);
                } else {
                    QList<QString> infoList({"DATABASE_NOT_CONNECTED", "MODE_NOT_SPECIFIED", "MODE_NOT_SUPPORTED", "DATA_NOT_SET", "EMAIL_DONT_EXIST", "HASH_MISMATCH", "MAIL_CANT_SEND", "PROTOCOL_UNKNOWN"});
                    switch(infoList.indexOf(responseObject["code"].toString())){
                        case 0:
                            ui->accInfoLabel->setText("ERROR: Could not connect to the server Database.");
                            break;
                        case 1:
                        case 2:
                        case 3:
                            ui->accInfoLabel->setText("ERROR: Data not send, retry operation or use More Actions from menu.");
                            break;
                        case 4:
                            ui->accInfoLabel->setText("ERROR: Typed Email dont exist in database, retry action.");
                            break;
                        case 5:
                            ui->accInfoLabel->setText("ERROR: Mismatched hashes, can't proceed with request.");
                            break;
                        case 6:
                            ui->accInfoLabel->setText("ERROR: Couldn't send activation email, try again using More Actions from menu.");
                            break;
                        case 7:
                            ui->accInfoLabel->setText("ERROR: Somehow wrong data transfer protocol was used, try again.");
                            break;
                    }
                }
            } else {
                ui->accInfoLabel->setText(reply->errorString());
            }
            QTimer::singleShot( 5000, this, SLOT(checkIfUserLogged()));
            clearInputs();
            reply->deleteLater();
        });
    }
}

void AccountsWindow::dbInit(){
    if(QSqlDatabase::database("db").open()) {
        setDatabaseInfo("Database successfully connected.");
    } else setDatabaseInfo(QSqlDatabase::database("db").lastError().text());
    checkIfUserLogged();
}

void AccountsWindow::clearInputs()
{
    ui->password->clear();
    ui->username->clear();
    ui->password_4->clear();
    ui->repeatPasswordLineEdit->clear();
    ui->emailLineEdit->clear();
}

void AccountsWindow::checkIfValidated()
{
    if (!ui->emailLineEdit->hasAcceptableInput()) ui->emailLineEdit->setStyleSheet("background-color: rgba(50, 58, 66, 1); border: 2px solid red;");
    else ui->emailLineEdit->setStyleSheet("background-color: rgba(50, 58, 66, 1); border: 2px solid green;");
}

void AccountsWindow::on_actionLogin_triggered()
{
    this->ui->stackedWidget->setCurrentIndex(0);
}

void AccountsWindow::on_actionRegister_triggered()
{
    this->ui->stackedWidget->setCurrentIndex(1);
}

void AccountsWindow::on_actionMore_Actions_triggered()
{
    QDesktopServices::openUrl(QUrl("https://quickfilehosting.ddns.net/account/"));
}

void AccountsWindow::on_loginButton_clicked()
{
    ui->loginButton->setDisabled(true);
    QTimer::singleShot(3000, this, [this]() {
        ui->loginButton->setDisabled(false);
    });
    QString username = ui->username->text();
    QString password = ui->password->text();
    clearInputs();

    if (!QSqlDatabase::database("db").isOpen()) QSqlDatabase::database("db").open();
    QSqlQuery querry (QSqlDatabase::database("db"));
    querry.prepare("SELECT * FROM users WHERE `username` = ?;");
    querry.bindValue(0, username);
    if(querry.exec() && querry.first() && crypto_pwhash_str_verify(querry.value("password").toString().toUtf8(), password.toUtf8(), password.length()) == 0) {
        if (querry.value("active").toBool()) setLoggedUser(querry.value("id").toInt(), username, ui->remembermeButton->isChecked());
        else {
            ui->accInfoLabel->setText("ERROR: Account not active.");
            QTimer::singleShot( 5000, this, SLOT(checkIfUserLogged()));
        }
    }
    else {
        ui->accInfoLabel->setText("ERROR: Wrong username and password.");
        QTimer::singleShot( 5000, this, SLOT(checkIfUserLogged()));
    }
}

void AccountsWindow::on_registerButton_4_clicked()
{
    ui->registerButton_4->setDisabled(true);
    QTimer::singleShot(3000, this, [this]() {
        ui->registerButton_4->setDisabled(false);
    });
    QString username = ui->username_4->text();
    QString password = ui->password_4->text();
    QString passwordRepeat = ui->repeatPasswordLineEdit->text();
    QString email = ui->emailLineEdit->text();

    if (password.length() < 8){
        ui->accInfoLabel->setText("ERROR: Password too short.");
        QTimer::singleShot( 5000, this, SLOT(checkIfUserLogged()));
        return;
    }

    if (username.length() < 6){
        ui->accInfoLabel->setText("ERROR: Username too short.");
        QTimer::singleShot( 5000, this, SLOT(checkIfUserLogged()));
        return;
    }

    if (password != passwordRepeat){
        ui->accInfoLabel->setText("ERROR: Passwords don't match.");
        QTimer::singleShot( 5000, this, SLOT(checkIfUserLogged()));
        return;
    }

    if (!ui->emailLineEdit->hasAcceptableInput()){
        ui->accInfoLabel->setText("ERROR: Email have wrong format.");
        QTimer::singleShot( 5000, this, SLOT(checkIfUserLogged()));
        return;
    }

    char hashedpassword[crypto_pwhash_STRBYTES];
    if (crypto_pwhash_str(hashedpassword, password.toUtf8(), password.length(), crypto_pwhash_OPSLIMIT_INTERACTIVE, crypto_pwhash_MEMLIMIT_INTERACTIVE) != 0) {
        ui->accInfoLabel->setText("ERROR: Couldn't secure password, try again.");
        QTimer::singleShot( 5000, this, SLOT(checkIfUserLogged()));
        return;
    }

    if (!QSqlDatabase::database("db").isOpen()) QSqlDatabase::database("db").open();
    QSqlQuery querry (QSqlDatabase::database("db"));
    querry.prepare("SELECT * FROM users WHERE `email` = ?;");
    querry.bindValue(0, email);
    if(querry.exec() && querry.first()) {
        ui->accInfoLabel->setText("ERROR: Email already in use.");
        QTimer::singleShot( 5000, this, SLOT(checkIfUserLogged()));
        return;
    }
    querry.finish();

    querry.prepare("SELECT * FROM users WHERE `username` = ?;");
    querry.bindValue(0, username);
    if(querry.exec() && querry.first()) {
        ui->accInfoLabel->setText("ERROR: Username already in use.");
        QTimer::singleShot( 5000, this, SLOT(checkIfUserLogged()));
        return;
    }
    querry.finish();

    querry.prepare("INSERT INTO `users`(`username`, `password`, `email`, `createdate`, `hash`) VALUES (?,?,?,?,?);");
    querry.bindValue(0, username);
    querry.bindValue(1, QString(hashedpassword));
    querry.bindValue(2, email);
    querry.bindValue(3, QDateTime::currentSecsSinceEpoch());
    QString tmpuid = (QUuid::createUuid()).toString(QUuid::WithoutBraces);
    querry.bindValue(4, tmpuid);
    if(querry.exec() && querry.numRowsAffected() == 1) {
        sendRequestForAccountConfirm(email, tmpuid);
        return;
    }
}

void AccountsWindow::on_logoutButton_clicked()
{
    this->currentUser->id = 0;
    this->currentUser->username = "anonymous";
    this->currentUser->hash = "";
    this->currentUser->hashValidUntill = 0;
    ui->actionLogin->setDisabled(false);
    ui->actionRegister->setDisabled(false);
    ui->stackedWidget->setCurrentIndex(0);
    clearInputs();
    QFile token(QStandardPaths::writableLocation(QStandardPaths::AppLocalDataLocation) + "/token.ini");
    if (token.exists()) token.remove();
    if (auto *parent = qobject_cast<MainWindow *>(this->parent())) {
        parent->loadLoggedUser();
    }
    this->close();
}
