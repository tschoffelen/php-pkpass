# PHP PKPass class for iOS 6 Passbook
This class provides the functionality to create an Pass for Passbook in Apple's iOS 6 on-the-fly. It creates, signs and packages the Pass as a .pkpass file according to Apple's documentation.

## Requirements
* PHP 5
* Access to OpenSSL and Zip via PHP's exec()
* Access to filesystem (script creates a directory `temp/` for temporary files)

## Usage
Please take a look at the example.php file for example usage. For more info on the JSON for the pass and how to style it, take a look at the [docs at developers.apple.com](https://developer.apple.com/library/prerelease/ios/documentation/UserExperience/Reference/PassKit_Bundle/Chapters/Introduction.html).

A live demo of the full sample can be found on [http://www.tomttb.com/test/pass/full_sample](http://www.tomttb.com/test/pass/full_sample).

Please note that iOS 6 and Passbook are still in beta, which means that the API can change at any moment. As new beta releases of iOS 6 come up, I'll try to update the class as soon as possible and add more features.

### Requesting the Pass Certificate
1. Go to the [iOS Provisioning portal](https://developer.apple.com/ios/manage/passtypeids/ios/manage)
2. Create a new Pass Type ID
3. Request the certificate like shown
4. Download the .cer file and drag it into Keychain Access
5. Right click the certificate in Keychain Access and choose `Export 'pass.<id>'…`
6. Choose a password and export the file to a folder

### Getting the example.php sample to work
1. Request the Pass certificate and upload it to your server
2. Set the correct path and password on [line 6 and 7](https://github.com/tschoffelen/PHP-PKPass/blob/master/example.php#L6)
3. Change the `passTypeIdentifier` and `teamIndentifier` to the correct values, which can be found on the [iOS Provisioning portal](https://developer.apple.com/ios/manage/passtypeids/ios/manage) after clicking on 'Configure' next to the Pass ID, on line [10](https://github.com/tschoffelen/PHP-PKPass/blob/master/example.php#L10) and [14](https://github.com/tschoffelen/PHP-PKPass/blob/master/example.php#L14)

After completing these steps, you should be ready to go. Upload all the files to your server and navigate to the address of the example.php file on your iPhone.


## Support
For info, mail me at tom@tomttb.com or tweet me [@tschoffelen](http://www.twitter.com/tschoffelen).
