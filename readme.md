# PHP PKPass class for iOS 6 Passbook
This class provides the functionality to create an Pass for Passbook in Apple's iOS 6 on-the-fly. It creates, signs and packages the Pass as a .pkpass file according to Apple's documentation.

## Requirements
* PHP 5
* Access to OpenSSL and Zip via PHP's exec()

## Usage
Please take a look at the example.php file for example usage. For more info on the JSON for the pass and how to style it, take a look at the [docs at developers.apple.com](https://developer.apple.com/library/prerelease/ios/#documentation/UserExperience/Reference/PassKit_Bundle/Chapters/Introduction.html).

Please note that iOS 6 and Passbook are still in beta, which means that the API can change at any moment. As new beta releases of iOS 6 come up, I'll try to update the class as soon as possible and add more features.

## Support
For info, mail me at tom@tomttb.com or tweet me via @tschoffelen.
