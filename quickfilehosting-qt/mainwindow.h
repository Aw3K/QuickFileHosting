#ifndef MAINWINDOW_H
#define MAINWINDOW_H

#include "fileuploader.h"
#include "qnetworkreply.h"
#include "uploadedfiles.h"
#include "accountswindow.h"
#include "userdata.h"
#include <QMainWindow>
#include <QListWidget>

QT_BEGIN_NAMESPACE
namespace Ui {
class MainWindow;
}
QT_END_NAMESPACE

class MainWindow : public QMainWindow
{
    Q_OBJECT

    public:
        MainWindow(QWidget *parent = nullptr, uploadedFiles* = nullptr, AccountsWindow* = nullptr, UserData* currentUser = nullptr);
        ~MainWindow();
        void uploadFile(const QString &filePath, const QUrl &serverUrl);
        void onReqFinished(QNetworkReply *reply);
        void reloadUploadedList();
        void setUploadInfo(QString status, QString code, QString info = nullptr);
        void setAccountInfo(QString code, QString info);

        uploadedFiles *list;
        AccountsWindow *acc;
        UserData *currentUser;
        QList<QString> filesLocallyUploaded;
        QUrl uploadUrl = QUrl("https://quickfilehosting.ddns.net/app/appupload.php");
        QUrl actionsUrl = QUrl("https://quickfilehosting.ddns.net/app/actions.php");
        QString rootUrl = "https://quickfilehosting.ddns.net/";
        int maxFileSize = 537919488;

    public slots:
        void loadLoggedUser();
        void setUploadStatus(qint64 bytesSent, qint64 bytesTotal);

    private slots:
        void on_pushButton_clicked();
        void on_tableWidget_cellDoubleClicked(int row, int column);
        void clearInfo();
        void on_actionAccount_triggered();
        void on_tableWidget_itemSelectionChanged();
        void on_comboBox_textActivated(const QString &arg1);

    private:
        Ui::MainWindow *ui;
        QWidget *iconWidget(QString str);
        void logUserWithToken(QString email, QString token);
        FileUploader *uploader;
};
#endif // MAINWINDOW_H
