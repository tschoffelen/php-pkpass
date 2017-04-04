[![OpenCollective](https://opencollective.com/php-pkpass/backers/badge.svg)](#backers) 
[![OpenCollective](https://opencollective.com/php-pkpass/sponsors/badge.svg)](#sponsors)

# PHP PKPass class for iOS Wallet
This class provides the functionality to create an Pass for Passbook in Apple's iOS 6 and newer on-the-fly. It creates, signs and packages the Pass as a .pkpass file according to Apple's documentation.

## Requirements
* PHP 5.4
* PHP [ZIP Support](http://php.net/manual/en/book.zip.php) (May be installed by default)
* Access to filesystem (Script must be able to create temporary folders)

## Installation
#### Composer
Run: `$ composer require pkpass/pkpass`

or add to your composer.json: `"pkpass/pkpass": "dev-master"`

#### Manual
Require PKPass.php file in your php files `require('PKPass.php');`

## Usage
Please take a look at the example.php file for example usage. For more info on the JSON for the pass and how to style it, take a look at the [docs at developers.apple.com](https://developer.apple.com/library/ios/documentation/UserExperience/Reference/PassKit_Bundle/Chapters/Introduction.html).

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
4. Change the `passTypeIdentifier` and `teamIndentifier` to the correct values, which can be found on the [iOS Provisioning portal](https://developer.apple.com/ios/manage/passtypeids/ios/manage) after clicking on 'Configure' next to the Pass ID, on line [15](https://github.com/tschoffelen/PHP-PKPass/blob/master/example.php#L15) and [17](https://github.com/tschoffelen/PHP-PKPass/blob/master/example.php#L17)

After completing these steps, you should be ready to go. Upload all the files to your server and navigate to the address of the example.php file on your iPhone.

## Included demos
* Simple example (example.php)
* Full example (full_sample/index.php)
* Starbucks example (starbucks_sample/index.php)

## Support & documentation
Please read the instructions above and consult the [Passbook Documentation](https://developer.apple.com/passbook/) before submitting tickets or requesting support. It might also be worth to [check Stackoverflow](http://stackoverflow.com/search?q=%22PHP-PKPass%22), which contains quite a few questions about this library.

Email me at thomas [at] scholica.com or tweet me [@tschoffelen](http://www.twitter.com/tschoffelen).

## Send me a coffee
Like my work? [Consider giving a small donation](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=X8Y8GRHBU7V8N). 

## Backers

Support us with a monthly donation and help us continue our activities. [[Become a backer](https://opencollective.com/php-pkpass#backer)]

<a href="https://opencollective.com/php-pkpass/backer/0/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/0/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/1/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/1/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/2/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/2/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/3/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/3/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/4/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/4/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/5/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/5/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/6/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/6/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/7/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/7/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/8/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/8/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/9/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/9/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/10/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/10/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/11/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/11/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/12/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/12/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/13/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/13/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/14/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/14/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/15/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/15/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/16/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/16/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/17/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/17/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/18/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/18/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/19/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/19/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/20/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/20/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/21/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/21/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/22/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/22/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/23/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/23/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/24/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/24/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/25/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/25/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/26/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/26/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/27/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/27/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/28/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/28/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/backer/29/website" target="_blank"><img src="https://opencollective.com/php-pkpass/backer/29/avatar.svg"></a>

## Sponsors

Become a sponsor and get your logo on our README on Github with a link to your site. [[Become a sponsor](https://opencollective.com/php-pkpass#sponsor)]

<a href="https://opencollective.com/php-pkpass/sponsor/0/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/0/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/1/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/1/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/2/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/2/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/3/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/3/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/4/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/4/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/5/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/5/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/6/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/6/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/7/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/7/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/8/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/8/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/9/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/9/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/10/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/10/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/11/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/11/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/12/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/12/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/13/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/13/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/14/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/14/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/15/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/15/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/16/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/16/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/17/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/17/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/18/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/18/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/19/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/19/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/20/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/20/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/21/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/21/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/22/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/22/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/23/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/23/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/24/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/24/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/25/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/25/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/26/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/26/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/27/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/27/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/28/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/28/avatar.svg"></a>
<a href="https://opencollective.com/php-pkpass/sponsor/29/website" target="_blank"><img src="https://opencollective.com/php-pkpass/sponsor/29/avatar.svg"></a>
