QT       += core gui network widgets core5compat sql

greaterThan(QT_MAJOR_VERSION, 4): QT += widgets

CONFIG += c++17

# You can make your code fail to compile if it uses deprecated APIs.
# In order to do so, uncomment the following line.
#DEFINES += QT_DISABLE_DEPRECATED_BEFORE=0x060000    # disables all the APIs deprecated before Qt 6.0.0

SOURCES += \
    accountswindow.cpp \
    fileuploader.cpp \
    main.cpp \
    mainwindow.cpp \
    uploadedfiles.cpp \
    userdata.cpp

HEADERS += \
    accountswindow.h \
    fileuploader.h \
    mainwindow.h \
    uploadedfiles.h \
    userdata.h

FORMS += \
    accountswindow.ui \
    mainwindow.ui

# Default rules for deployment.
qnx: target.path = /tmp/$${TARGET}/bin
else: unix:!android: target.path = /opt/$${TARGET}/bin
!isEmpty(target.path): INSTALLS += target
RC_ICONS = icon.ico
VERSION = 1.0.1.4

RESOURCES += \
    images.qrc

LIBS += -LE:/Qt/libsodium-win64/lib -lsodium

INCLUDEPATH += E:/Qt/libsodium-win64/include
DEPENDPATH += E:/Qt/libsodium-win64
