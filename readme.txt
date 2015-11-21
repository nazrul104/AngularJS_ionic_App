 keytool -genkey -v -keystore curryworldindian.keystore -alias curryworldindian.com -keyalg RSA -keysize 2048 -validity 10000
 
setting:
========
key: curryworldindian.keystore
keystore pass:12345678
alias:mughaldynasty.com
packageid(app id):com.curryworldindian
version:1.0
To Sign unsigned app:
====================
jarsigner -verbose -sigalg SHA1withRSA -digestalg SHA1 -keystore F:\ionic\TestPad\curry_world_a1_v1\curryworldindian.keystore F:\ionic\TestPad\curry_world_a1_v1\platforms\android\build\outputs\apk\android-release-unsigned.apk curryworldindian.com

C:\Users\najrul\AppData\Local\Android\sdk\build-tools\22.0.1\zipalign.exe -v 4 android-release-unsigned.apk curryworld.apk

