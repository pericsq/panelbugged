<?php
/**HTML Purifier 4.9.3 released
Posted 1:38 AM EDT on Monday, June 19, 2017
HTML Purifier 4.9.3 is a bugfix release, which works around an infinite loop in PHP 7.1 that occurs when the opcode cache is enabled. Please see the upstream PHP bug for more information. There is also a minor bugfix to avoid hitting an autoloader when the dom extension is not available.

See NEWS for a complete changelog.

Permalink
HTML Purifier 4.9.2 released
Posted 2:43 AM EDT on Monday, March 13, 2017
HTML Purifier 4.9.2 is a bugfix release, which fixes two regressions in 4.9.1; first, entity decoding of non-hexadecimal numeric entities no longer causes HTML Purifier to throw an exception, second, HTML Purifier 4.9.2 is once again PHP 5.3 compatible.

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

See NEWS for a complete changelog. */
require dirname(__FILE__) . '/CacheTarget.php';
/** Permalink
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

*/
?>