#include "mainwindow.h"
#include "accountswindow.h"
#include "qlineedit.h"
#include "qsqlquery.h"
#include "fileuploader.h"
#include "ui_mainwindow.h"
#include "uploadedfiles.h"
#include "userdata.h"
#include <QUrl>
#include <QFileDialog>
#include <QTableWidget>
#include <QTableWidgetItem>
#include <QVBoxLayout>
#include <QLabel>
#include <QDesktopServices>
#include <QTimer>
#include <QStandardPaths>
#include <QInputDialog>
#include <QClipboard>

MainWindow::MainWindow(QWidget *parent, uploadedFiles *list, AccountsWindow *acc, UserData *currentUser)
    : QMainWindow(parent)
    , ui(new Ui::MainWindow)
{
    ui->setupUi(this);
    this->list = list;
    this->acc = acc;
    this->currentUser = currentUser;
    ui->comboBox->setHidden(true);
    ui->uploadProgressBar->setHidden(true);
    this->setFixedSize(QSize(650, 430));
    loadLoggedUser();
    this->uploader = new FileUploader(this, list, currentUser);
}

MainWindow::~MainWindow()
{
    delete ui;
}

QWidget* MainWindow::iconWidget(QString str){
    QIcon Icon(str);
    QTableWidgetItem *item = new QTableWidgetItem();
    item->setData(Qt::DecorationRole, QVariant(Icon));

    QWidget *widget = new QWidget();
    QVBoxLayout *layout = new QVBoxLayout(widget);

    QLabel *label = new QLabel();
    label->setPixmap(Icon.pixmap(QSize(20, 20)));
    label->setAlignment(Qt::AlignCenter);

    layout->addWidget(label);
    layout->setAlignment(Qt::AlignCenter);
    layout->setContentsMargins(1,1,1,1);

    return widget;
}

void MainWindow::logUserWithToken(QString username, QString token)
{
    if (username.isEmpty() || username.isNull() || token.isEmpty() || token.isNull()){
        setAccountInfo("LOCAL_DATA_ERROR", "");
        return;
    }
    if (!QSqlDatabase::database("db").isOpen()) QSqlDatabase::database("db").open();
    QSqlQuery querry (QSqlDatabase::database("db"));
    querry.prepare("SELECT * FROM users WHERE `username` = ? AND `hash` = ?;");
    querry.bindValue(0, username);
    querry.bindValue(1, token);
    if(querry.exec() && querry.first()) {
        if (querry.value("hash_valid_time").toInt() > QDateTime::currentSecsSinceEpoch()){
            currentUser->id = querry.value("id").toInt();
            currentUser->username = querry.value("username").toString();
            currentUser->hash = token;
            list->readListFromAccount();
        } else {
            QFile token(QStandardPaths::writableLocation(QStandardPaths::AppLocalDataLocation) + "/token.ini");
            if (token.exists()) token.remove();
            setAccountInfo("LOCAL_DATA_EXPIRED_OR_LOST", "");
            currentUser->id = -1;
        }
    } else {
        currentUser->id = -1;
        setAccountInfo("LOCAL_DATA_EXPIRED_OR_LOST", "");
    }
    loadLoggedUser();
}

void MainWindow::reloadUploadedList()
{
    ui->tableWidget->setRowCount(0);
    ui->tableWidget->setEditTriggers(QAbstractItemView::NoEditTriggers);
    if (currentUser->id < 1){
        ui->tableWidget->setColumnCount(6);
        ui->tableWidget->setColumnWidth(4,1);
        ui->tableWidget->setColumnWidth(5,1);
        QStringList labels;
        labels << "Token" << "Name" << "Size" << "Expires" << "" << "";
        ui->tableWidget->setHorizontalHeaderLabels(labels);
        ui->tableWidget->horizontalHeader()->setSectionResizeMode(QHeaderView::ResizeToContents);
        ui->tableWidget->horizontalHeader()->setSectionResizeMode(1, QHeaderView::Stretch);
        for(int i = 0; i<list->uploadFileDataList.size(); i++){
            ui->tableWidget->insertRow(i);
            auto item = new QTableWidgetItem(list->uploadFileDataList[i].name);
            if(list->uploadFileDataList[i].remove == "true") item->setForeground(QBrush(QColor("#4f545a")));
            ui->tableWidget->setItem(i,1, item);

            item = new QTableWidgetItem(list->uploadFileDataList[i].token);
            if(list->uploadFileDataList[i].remove == "true") item->setForeground(QBrush(QColor("#4f545a")));
            ui->tableWidget->setItem(i,0, item);

            QString itemFormat;
            if (list->uploadFileDataList[i].size < 1048576) itemFormat = QString::number(list->uploadFileDataList[i].size/1024.0,'l',1) + QString("KB");
            else itemFormat = QString::number(list->uploadFileDataList[i].size/1024.0/1024.0,'l',1) + QString("MB");
            item = new QTableWidgetItem(itemFormat);
            if(list->uploadFileDataList[i].remove == "true") item->setForeground(QBrush(QColor("#4f545a")));
            ui->tableWidget->setItem(i,2, item);

            item = new QTableWidgetItem(list->uploadFileDataList[i].expires);
            if(list->uploadFileDataList[i].remove == "true") item->setForeground(QBrush(QColor("#4f545a")));
            ui->tableWidget->setItem(i,3, item);

            ui->tableWidget->setCellWidget(i,4, iconWidget(":/images/remicon.png"));
            ui->tableWidget->setCellWidget(i,5, iconWidget(":/images/downloadicon.png"));
        }
    } else {
        ui->tableWidget->setColumnCount(6);
        ui->tableWidget->setColumnWidth(5,1);
        ui->tableWidget->setColumnWidth(6,1);
        QStringList labels;
        labels << "Token" << "Name" << "Size" << "Expires" << "Pass" << "" << "";
        ui->tableWidget->setHorizontalHeaderLabels(labels);
        ui->tableWidget->horizontalHeader()->setSectionResizeMode(QHeaderView::ResizeToContents);
        ui->tableWidget->horizontalHeader()->setSectionResizeMode(1, QHeaderView::Stretch);
        for(int i = 0; i<list->uploadFileDataList.size(); i++){
            ui->tableWidget->insertRow(i);
            auto item = new QTableWidgetItem(list->uploadFileDataList[i].name);
            if(list->uploadFileDataList[i].remove == "true") item->setForeground(QBrush(QColor("#4f545a")));
            ui->tableWidget->setItem(i,1, item);

            item = new QTableWidgetItem(list->uploadFileDataList[i].token);
            if(list->uploadFileDataList[i].remove == "true") item->setForeground(QBrush(QColor("#4f545a")));
            ui->tableWidget->setItem(i,0, item);

            QString itemFormat;
            if (list->uploadFileDataList[i].size < 1048576) itemFormat = QString::number(list->uploadFileDataList[i].size/1024.0,'l',1) + QString("KB");
            else itemFormat = QString::number(list->uploadFileDataList[i].size/1024.0/1024.0,'l',1) + QString("MB");
            item = new QTableWidgetItem(itemFormat);
            if(list->uploadFileDataList[i].remove == "true") item->setForeground(QBrush(QColor("#4f545a")));
            ui->tableWidget->setItem(i,2, item);

            item = new QTableWidgetItem(list->uploadFileDataList[i].expires);
            if(list->uploadFileDataList[i].remove == "true") item->setForeground(QBrush(QColor("#4f545a")));
            ui->tableWidget->setItem(i,3, item);

            item = new QTableWidgetItem((list->uploadFileDataList[i].isPassLocked) ? "true" : "");
            if(list->uploadFileDataList[i].remove == "true") item->setForeground(QBrush(QColor("#4f545a")));
            ui->tableWidget->setItem(i,4, item);

            ui->tableWidget->setCellWidget(i,5, iconWidget(":/images/downloadicon.png"));
        }
    }
}

void MainWindow::loadLoggedUser(){
    if(currentUser->id == 0) {
        QFile token(QStandardPaths::writableLocation(QStandardPaths::AppLocalDataLocation) + "/token.ini");
        if (token.exists() && token.open(QIODevice::ReadOnly)){
            QString tokenString = token.readLine();
            QList<QString> tokenData = tokenString.split(";");
            if (tokenData.size() == 2){
                token.close();
                logUserWithToken(tokenData[0], tokenData[1]);
            }
        } else {
            setAccountInfo("APP_GUEST", "");
            list->readListFromFile();
            reloadUploadedList();
        }
    }
    else if (currentUser->id == -1) {
        setAccountInfo("APP_ACCOUNT_ERROR", "");
        list->readListFromAccount();
        reloadUploadedList();
    }
    else {
        setAccountInfo("APP_LOGGED_IN", currentUser->username);
        list->readListFromAccount();
        reloadUploadedList();
    }
    ui->comboBox->setHidden(true);
}

void MainWindow::setAccountInfo(QString code, QString info){
    QList<QString> infoList({"APP_GUEST", "APP_ACCOUNT_ERROR", "APP_LOGGED_IN", "LOCAL_DATA_ERROR", "LOCAL_DATA_EXPIRED_OR_LOST", "LOCAL_DATA_CREATE_ERROR"});
    switch(infoList.indexOf(code)){
    case 0:
        ui->mainWindowAccInfo->setText("Currently You are using application as guest.");
        break;
    case 1:
        ui->mainWindowAccInfo->setText("There were an error while loading user data. Try again.");
        break;
    case 2:
        ui->mainWindowAccInfo->setText("Logged in as: " + info);
        break;
    case 3:
        ui->mainWindowAccInfo->setText("Local userdata is not readable. Login again.");
        break;
    case 4:
        ui->mainWindowAccInfo->setText("Local userdata expired. Login again.");
        break;
    case 5:
        ui->mainWindowAccInfo->setText("Couldn't save userdata locally.");
        break;
    }
}

void MainWindow::on_pushButton_clicked()
{
    QString fileName = QFileDialog::getOpenFileName(this, tr("Select File"));

    if (fileName == "" || fileName.isEmpty()) return;
    QFile tmp(fileName);

    if (tmp.size() > this->maxFileSize) {
        setUploadInfo("ERROR", "FILE_TOO_BIG");
        return;
    }

    if (!tmp.exists()) {
        setUploadInfo("ERROR", "FILE_NAME_EMPTY");
        return;
    }
    if (this->uploader) {
        this->uploader->uploadFile(fileName, uploadUrl, this->currentUser);
    } else setUploadInfo("ERROR", "UPLOADER_NOT_INITIALIZED");
}

void MainWindow::on_tableWidget_cellDoubleClicked(int row, int column)
{
    if(column == 0) {
        QClipboard* clipboard = QApplication::clipboard();
        clipboard->setText(this->rootUrl + "x/" + list->uploadFileDataList[row].token);
        setUploadInfo("SUCCESS", "CLIPBOARD_COPY", list->uploadFileDataList[row].token);
    }
    if(column == 4 && currentUser->id < 1) {
        list->uploadFileDataList.removeAt(row);
        reloadUploadedList();
        list->saveListToFile();
    }
    if(column == 5) {
        QString url = rootUrl + "x/" + list->uploadFileDataList[row].token;
        QDesktopServices::openUrl(QUrl(url));
    }
}

void MainWindow::setUploadInfo(QString status, QString code, QString info){
    QList<QString> infoList({"DATABASE_NOT_CONNECTED", "FILE_TOO_BIG", "FILE_NAME_EMPTY", "FILE_UPLOADED_NOUSER", "FILE_UPLOADED_USER", "FILE_MOVE_FAILED", "FILE_UPLOAD_FAILED", "PROTOCOL_UNKNOWN", "CLIPBOARD_COPY", "UPLOADER_NOT_INITIALIZED"});
    if (status == "SUCCESS"){
        switch(infoList.indexOf(code)){
            case 3:
            case 4:
                ui->uploadInfoLabel->setText(" File successfuly uploaded to server.");
                break;
            case 8:
                ui->uploadInfoLabel->setText(" Link to file(" + info + ") copied to clipboard.");
                break;
        }
    } else {
        switch(infoList.indexOf(code)){
            case 0:
                ui->uploadInfoLabel->setText(" ERROR: Could not connect to the server Database.");
                break;
            case 1:
                ui->uploadInfoLabel->setText(" ERROR: Sent file was too big, max 512MB.");
                break;
            case 2:
                ui->uploadInfoLabel->setText(" ERROR: File name was an empty string.");
                break;
            case 5:
                ui->uploadInfoLabel->setText(" ERROR: Could not move file to it's new location on the server.");
                break;
            case 6:
                ui->uploadInfoLabel->setText(" ERROR: " + info);
                break;
            case 7:
                ui->uploadInfoLabel->setText(" ERROR: Somehow wrong data transfer protocol was used.");
                break;
            case 9:
                ui->uploadInfoLabel->setText(" ERROR: Uploader not initialized, restart app.");
                break;
        }
    }
    QTimer::singleShot(10000, this, SLOT(clearInfo()));
}

void MainWindow::clearInfo()
{
    ui->uploadInfoLabel->clear();
}

void MainWindow::setUploadStatus(qint64 bytesSent, qint64 bytesTotal)
{
    if(bytesSent == 0 && bytesTotal == 0) ui->uploadProgressBar->setHidden(true);
    else {
        ui->uploadProgressBar->setHidden(false);
        ui->uploadProgressBar->setRange(0,bytesTotal);
        ui->uploadProgressBar->setValue(bytesSent);
    }
}

void MainWindow::on_actionAccount_triggered()
{
    acc->show();
    acc->dbInit();
    acc->checkIfUserLogged();
}

void MainWindow::on_tableWidget_itemSelectionChanged()
{
    if (currentUser->id < 1) return;
    ui->comboBox->setCurrentIndex(0);
    auto selected = ui->tableWidget->selectedRanges();
    if (selected.isEmpty()){
        ui->comboBox->setHidden(true);
    } else {
        ui->comboBox->setHidden(false);
    }
}

void MainWindow::on_comboBox_textActivated(const QString &arg1)
{
    if (currentUser->id < 1) return;
    auto selected = ui->tableWidget->selectedRanges();
    QList<QString> actionList({"Delete", "Set Password", "Remove Password", "Recover"});
    bool confirm = false;
    QString text;
    if (actionList.indexOf(arg1) == 1) text = QInputDialog::getText(this, "Secure files", "Password:", QLineEdit::Password, "", &confirm);
    for (int i = 0; i<selected.size(); i++){
        for(int j = selected[i].topRow(); j <= selected[i].bottomRow(); j++){
            switch(actionList.indexOf(arg1)){
                case 0:
                    list->setRemoveStatus(j, "true");
                    break;
                case 1:
                    if (confirm) list->setPassword(j, text);
                    break;
                case 2:
                    list->setPassword(j, "");
                    break;
                case 3:
                    list->setRemoveStatus(j, "false");
                    break;
            }
        }
    }
    list->readListFromAccount();
    reloadUploadedList();
}

