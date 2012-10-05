# PHP PKPass class for iOS 6 Passbook
This class provides the functionality to create an Pass for Passbook in Apple's iOS 6 and newer on-the-fly. It creates, signs and packages the Pass as a .pkpass file according to Apple's documentation.

Please also take a look at [my repository PHP-Passkit](https://github.com/tschoffelen/PHP-Passkit), which includes the PKTemplate and PKValidate classes, to easily manage templates and validate pass files.

## Requirements
* PHP 5
* PHP [ZIP Support](http://php.net/manual/en/book.zip.php) (May be installed by default)
* Access to filesystem (Script must be able to create temporary folders)

## Usage
Please take a look at the example.php file for example usage. For more info on the JSON for the pass and how to style it, take a look at the [docs at developers.apple.com](https://developer.apple.com/library/prerelease/ios/documentation/UserExperience/Reference/PassKit_Bundle/Chapters/Introduction.html).

Demo:
* Simple example (example.php): [http://apps.tomttb.com/pkpass/example.php](http://apps.tomttb.com/pkpass/example.php)
* Full example (full_sample/index.php): [http://apps.tomttb.com/pkpass/full_sample](http://apps.tomttb.com/pkpass/full_sample)
* Starbucks example (starbucks_sample/index.php): [http://apps.tomttb.com/pkpass/starbucks_sample](http://apps.tomttb.com/pkpass/starbucks_sample)

### Requesting the Pass Certificate
1. Go to the [iOS Provisioning portal](https://developer.apple.com/ios/manage/passtypeids/ios/manage)
2. Create a new Pass Type ID
3. Request the certificate like shown
4. Download the .cer file and drag it into Keychain Access
5. Right click the certificate in Keychain Access and choose `Export 'pass.<id>'…`
6. Choose a password and export the file to a folder

### Getting the example.php sample to work
1. Request the Pass certificate (`.p12`) and upload it to your server
2. Set the correct path and password on [line 6 and 7](https://github.com/tschoffelen/PHP-PKPass/blob/master/example.php#L6)
3. Download and import your [WWDR Intermediate certificate](https://developer.apple.com/certificationauthority/AppleWWDRCA.cer) to Keychain, export as `.pem` and set the correct path on [line 8](https://github.com/tschoffelen/PHP-PKPass/blob/master/example.php#L8)
4. Change the `passTypeIdentifier` and `teamIndentifier` to the correct values, which can be found on the [iOS Provisioning portal](https://developer.apple.com/ios/manage/passtypeids/ios/manage) after clicking on 'Configure' next to the Pass ID, on line [11](https://github.com/tschoffelen/PHP-PKPass/blob/master/example.php#L10) and [15](https://github.com/tschoffelen/PHP-PKPass/blob/master/example.php#L14)

After completing these steps, you should be ready to go. Upload all the files to your server and navigate to the address of the example.php file on your iPhone.


## Support
Please add an Issue to this Github repository, mail me at tom [at] tomttb.com or tweet me [@tschoffelen](http://www.twitter.com/tschoffelen).
