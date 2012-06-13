# PHP PKPass class for iOS 6 Passbook
This class provides the functionality to create an Pass for Passbook in Apple's iOS 6 on-the-fly. It creates, signs and packages the Pass as a .pkpass file according to Apple's documentation.

## Requirements
* PHP 5
* Access to OpenSSL and Zip via PHP's exec()
* Access to filesystem (script creates a directory temp/ for temporary files)

## Usage
Please take a look at the example.php file for example usage. For more info on the JSON for the pass and how to style it, take a look at the [docs at developers.apple.com](https://developer.apple.com/library/prerelease/ios/#documentation/UserExperience/Reference/PassKit_Bundle/Chapters/Introduction.html).

Please note that iOS 6 and Passbook are still in beta, which means that the API can change at any moment. As new beta releases of iOS 6 come up, I'll try to update the class as soon as possible and add more features.

### Requesting the Pass Certificate
1. Go to the [iOS Provisioning portal](https://developer.apple.com/ios/manage/passtypeids/ios/manage)
2. Create a new Pass Type ID
3. Request the certificate like shown
4. Download the .cer file and drag it into Keychain Access
5. Right click the certificate in Keychain Access and choose 'export'
6. Choose a password and export the file to a folder

## Support
For info, mail me at tom@tomttb.com or tweet me via @tschoffelen.
