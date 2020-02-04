# PHP library to create passes for iOS wallet app
This class provides the functionality to create passes for Wallet in Apple's iOS 6 and newer on-the-fly. It creates, signs and packages the pass as a `.pkpass` file according to Apple's documentation.

## Requirements
* PHP 5.6 or higher
* PHP [ZIP Support](http://php.net/manual/en/book.zip.php) (may be installed by default)
* Access to filesystem (script must be able to create temporary folders)


## Installation

#### Composer
Simply run the following command in your project's root directory to install via [Composer](https://getcomposer.org/):

```
composer require pkpass/pkpass
```

Or add to your composer.json: `"pkpass/pkpass": "^1.2.0"`


#### Manually
Include the `PKPass.php` file in your PHP project:

```
require('src/PKPass.php');
```


## Usage
Please take a look at the example.php file for example usage. For more info on the JSON for the pass and how to style it, take a look at the [docs at developers.apple.com](https://developer.apple.com/library/ios/documentation/UserExperience/Reference/PassKit_Bundle/Chapters/Introduction.html).

### Requesting the Pass Certificate
1. Go to the [iOS Provisioning portal](https://developer.apple.com/account/ios/identifier/passTypeId).
2. Create a new Pass Type ID, and write down the Pass ID you choose, you'll need it later.
3. Click the edit button under your newly created Pass Type ID and generate a certificate according to the instructions shown on the page. Make sure *not* to choose a name for the Certificate but keep it empty instead.
4. Download the .cer file and drag it into Keychain Access.
5. Find the certificate you just imported and click the triangle on the left to reveal the private key.
6. Select both the certificate and the private key under it, then right click the certificate in Keychain Access and choose `Export 2 items…`.
6. Choose a password and export the file to a folder.

<img src="examples/guide-export.gif" title="Exporting P12 file" width="540" />

### Getting the example.php sample to work
1. Request the Pass certificate (`.p12`) as described above and upload it to your server.
2. Set the correct path and password on [line 22](examples/example.php#L22).
3. Change the `passTypeIdentifier` and `teamIndentifier` to the correct values on lines [29](examples/example.php#L29) and [31](examples/example.php#L31) (`teamIndentifier` can be found on the [Developer Portal](https://developer.apple.com/account/#/membership)).

After completing these steps, you should be ready to go. Upload all the files to your server and navigate to the address of the examples/example.php file on your iPhone.


## Included demos
* 📦 [Simple example](examples/example.php)
* ✈️ [Flight ticket example](examples/full_sample/)
* ☕️ [Starbucks card example](examples/starbucks_sample/)


## Functions to add files
* `addFile` : add a file without locale like `icon.png`
* `addRemoteFile` : add a file from a url without locale like `https://xyz.io/icon.png`
* `addLocaleFile` : add a localized file like `strip.png`
* `addLocaleRemoteFile` : add a localized file from a url like `https://xyz.io/strip.png`

## Debugging passes
If you aren't able to open your pass on an iPhone, plug the iPhone into a Mac and open the 'Console' application. On the left, you can select your iPhone. You will then be able to inspect any errors that occur while adding the pass:

<img src="https://s3-eu-west-1.amazonaws.com/tsfil/Screen-Shot-2017-04-29-01-32-14-SrVhh/Screen-Shot-2017-04-29-01-32-14.png" title="Console with Passkit error" width="540" />

* `Trust evaluate failure: [leaf TemporalValidity]`: If you see this error, your pass was signed with an outdated certificate.
* `Trust evaluate failure: [leaf LeafMarkerOid]`: You did not leave the name of the certificate empty while creating it in the developer portal. 

## Support & documentation
Please read the instructions above and consult the [Wallet Documentation](https://developer.apple.com/wallet/) before submitting tickets or requesting support. It might also be worth to [check Stackoverflow](http://stackoverflow.com/search?q=%22PHP-PKPass%22), which contains quite a few questions about this library.

Email me at thomas [at] scholica.com or tweet me [@tschoffelen](http://www.twitter.com/tschoffelen).
