# Salesforce Leads Changelog

All notable changes to this project will be documented in this file.

## 1.2.0 - 2018-12-06

## Added
- `isSpam` event property that if set to true, causes the submission to fail silently

## Changed
- `postService` is now `post`
- `validationService` is now `validation`
- `logService` is now `log`
- `Submission handled` log message now handled by post service

## 1.1.4 - 2018-09-10

### Fixed
- Some fields were being unset before being posted to Salesforce

## 1.1.3 - 2018-08-14

### Changed
- OID and Honeypot values are no longer returned in the response

## 1.1.2 - 2018-08-13

### Fixed
- Submission was marked as valid if email passed validation even though it failed honeypot validation

## 1.1.1 - 2018-08-13

### Fixed
- Composer misconfigured

## 1.1.0 - 2018-08-13

### Added
- Honeypot Captcha
- [Email Validation](https://github.com/lukeyouell/craft-emailvalidator) support
- Submissions are now logged and available from the Utilities section

## 1.0.2 - 2018-04-16

### Changed
- New icon
- Dropped Craft CMS minimum requirement to `^3.0.0`

## 1.0.1 - 2018-04-11

### Added
- Added beforeSave and afterSave events

### Changed
- Set Craft CMS minimum requirement to `^3.0.1`

## 1.0.0 - 2018-02-13

### Added
- Initial release
