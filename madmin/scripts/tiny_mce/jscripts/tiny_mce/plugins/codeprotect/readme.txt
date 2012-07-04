ABOUT

This simple plugin protects any code in the html you are editing, by turning 
it's start- and end-tag into comment tags when the editor is started.

<? becomes <!--CODE
?> becomes CODE-->

The bits in between the tags get encoded, so that the HTML parser won't go
and helpfully 'clean up' all your carefully formed code.

Eg: If you have something like this: 
		echo "<td nowrap>"; 
it won't get encoded into: 
		echo "<td nowrap="nowrap">";

The plugin changes the tags back to normal when you save your html.

If you need this plugin to be more complex, feel free to expand on this 
plugin, it's easier than you think. If you do make it better, please 
upload it to sourceforge.


INSTALL

To install, copy the codeprotect folder to the plugins directory and just 
add the word "codeprotect" to the list of plugins in the "init" bit of your tinyMCE 
page. The plugin doesn't have a button, so no need to worry about that.


tested with tinyMCE 2.0 rc3, tinyMCE 3.2.7

Tijmen Schep, 9 october 2005

Updated for tinyMCE 3.x by Greg Smith, UK, 19 Feb 2008
Updated to stop code being munged by Ben Hitchcock, Australia, 30 Oct 2009
Updated for tinyMCE 3.3.2 by Ben Hitchcock, Australia, 31 May 2011