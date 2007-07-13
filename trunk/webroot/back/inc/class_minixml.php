<?php

/***************************************************************************************************
****************************************************************************************************
*****
*****      MiniXML - PHP class library for generating and parsing XML.
*****
*****      Copyright (C) 2002-2005 Patrick Deegan, Psychogenic.com
*****      All rights reserved.
*****
*****      http://minixml.psychogenic.com
*****
*****   This program is free software; you can redistribute
*****   it and/or modify it under the terms of the GNU
*****   General Public License as published by the Free
*****   Software Foundation; either version 2 of the
*****   License, or (at your option) any later version.
*****
*****   This program is distributed in the hope that it will
*****   be useful, but WITHOUT ANY WARRANTY; without even
*****   the implied warranty of MERCHANTABILITY or FITNESS
*****   FOR A PARTICULAR PURPOSE.  See the GNU General
*****   Public License for more details.
*****
*****   You should have received a copy of the GNU General
*****   Public License along with this program; if not,
*****   write to the Free Software Foundation, Inc., 675
*****   Mass Ave, Cambridge, MA 02139, USA.
*****
*****
*****   You may contact the author, Pat Deegan, through the
*****   contact section at http://www.psychogenic.com
*****
*****   Much more information on using this API can be found on the
*****   official MiniXML website - http://minixml.psychogenic.com
*****	or within the Perl version (XML::Mini) available through CPAN
*****
****************************************************************************************************
***************************************************************************************************/



/***************************************************************************************************
****************************************************************************************************
*****
*****					      CONFIGURATION
*****
*****  Please see the http://minixml.psychogenic.com website for details on these configuration
*****  options.
*****
****************************************************************************************************
***************************************************************************************************/


/* All config options can be set to 0 (off) or 1 (on) */

define("MINIXML_CASESENSITIVE", 0); /* Set to 1 to use case sensitive element name comparisons */

define("MINIXML_AUTOESCAPE_ENTITIES", 1); /* Set to 1 to autoescape stuff like > and < and & in text, 0 to turn it off */



define("MINIXML_AUTOSETPARENT", 0); /* Set to 1 to automatically register parents elements with children */

define("MINIXML_AVOIDLOOPS", 0); /* Set to 1 to set the default behavior of 'avoidLoops' to ON, 0 otherwise */

define("MINIXML_IGNOREWHITESPACES", 1); /* Set to 1 to eliminate leading and trailing whitespaces from strings */


/* Lower/upper case attribute names.  Choose UPPER or LOWER or neither - not both... UPPER takes precedence */
define("MINIXML_UPPERCASEATTRIBUTES", 0); /* Set to 1 to UPPERCASE all attributes, 0 otherwise */
define("MINIXML_LOWERCASEATTRIBUTES", 0); /* Set to 1 to lowercase all attributes, 0 otherwise */


/* fromFile cache.
** If you are using lots of $xmlDoc->fromFile('path/to/file.xml') calls, it is possible to use
** a caching mechanism.  This cache will read the file, store a serialized version of the resulting
** object and read in the serialize object on subsequent calls.
**
** If the original XML file is updated, the cache will automatically be refreshed.
**
** To use caching, set MINIXML_USEFROMFILECACHING to 1 and set the
** MINIXML_FROMFILECACHEDIR to a suitable directory in which the cache files will
** be stored (eg, "/tmp")
**/
define("MINIXML_USEFROMFILECACHING", 0);
define("MINIXML_FROMFILECACHEDIR", "/tmp");


define("MINIXML_DEBUG", 0); /* Set Debug to 1 for more verbose output, 0 otherwise */


/*****************************************  end Configuration ***************************************/

define("MINIXML_USE_SIMPLE", 0);

define("MINIXML_VERSION", "1.3.0"); /* Version information */

define("MINIXML_NOWHITESPACES", -999); /* Flag that may be passed to the toString() methods */



$MiniXMLLocation = dirname(__FILE__);
//define("MINIXML_CLASSDIR", "$MiniXMLLocation/classes");
//require_once(MINIXML_CLASSDIR . "/doc.inc.php");


/***************************************************************************************************
****************************************************************************************************
*****
*****			           Global Helper functions
*****
****************************************************************************************************
***************************************************************************************************/


function _MiniXMLLog ($message)
{
	error_log("MiniXML LOG MESSAGE:\n$message\n");
}





function _MiniXMLError ($message)
{
	error_log("MiniXML ERROR:\n$message\n");

	return NULL;

}


function _MiniXML_NumKeyArray (&$v)
{
	if (! is_array($v))
	{
		return NULL;
	}


	$arrayKeys = array_keys($v);
	$numKeys = count($arrayKeys);
	$totalNumeric = 0;
	for($i=0; $i<$numKeys; $i++)
	{
		if (is_numeric($arrayKeys[$i]) && $arrayKeys[$i] == $i)
		{
			$totalNumeric++;
		} else {
			return 0;
		}
	}

	if ($totalNumeric == $numKeys)
	{
		// All numeric - assume it is a "straight" array
		return 1;
	} else {
		return 0;
	}
}




/***************************************************************************************************
****************************************************************************************************
*****
*****      MiniXML - PHP class library for generating and parsing XML.
*****
*****      Copyright (C) 2002-2005 Patrick Deegan, Psychogenic.com
*****      All rights reserved.
*****
*****      http://minixml.psychogenic.com
*****
*****   This program is free software; you can redistribute
*****   it and/or modify it under the terms of the GNU
*****   General Public License as published by the Free
*****   Software Foundation; either version 2 of the
*****   License, or (at your option) any later version.
*****
*****   This program is distributed in the hope that it will
*****   be useful, but WITHOUT ANY WARRANTY; without even
*****   the implied warranty of MERCHANTABILITY or FITNESS
*****   FOR A PARTICULAR PURPOSE.  See the GNU General
*****   Public License for more details.
*****
*****   You should have received a copy of the GNU General
*****   Public License along with this program; if not,
*****   write to the Free Software Foundation, Inc., 675
*****   Mass Ave, Cambridge, MA 02139, USA.
*****
*****
*****   You may contact the author, Pat Deegan, through the
*****   contact section at http://www.psychogenic.com
*****
*****   Much more information on using this API can be found on the
*****   official MiniXML website - http://minixml.psychogenic.com
*****	or within the Perl version (XML::Mini) available through CPAN
*****
****************************************************************************************************
***************************************************************************************************/


/*

#define("MINIXML_COMPLETE_REGEX",'/<\s*([^\s>]+)([^>]+)?>(.*?)<\s*\/\\1\s*>\s*([^<]+)?(.*)|\s*<!--(.+?)-->\s*|^\s*<\s*([^\s>]+)([^>]*)\/\s*>\s*([^<>]+)?|<!\[CDATA\s*\[(.*?)\]\]\s*>|<!DOCTYPE\s*([^\[]*)\[(.*?)\]\s*>|<!ENTITY\s*([^"\'>]+)\s*(["\'])([^\14]+)\14\s*>|^([^<]+)(.*)/smi');
*/

define("MINIXML_COMPLETE_REGEX",'/^\s*<\s*([^\s>]+)(\s+[^>]+)?>(.*?)<\s*\/\1\s*>\s*([^<]+)?(.*)|^\s*<!--(.+?)-->\s*(.*)|^\s*<\s*([^\s>]+)([^>]+)\/\s*>\s*(.*)|^\s*<!\[CDATA\s*\[(.*?)\]\]\s*>\s*(.*)|^\s*<!DOCTYPE\s*([^\[]*)\[(.*?)\]\s*>\s*(.*)|^\s*<!ENTITY\s*([^"\'>]+)\s*(["\'])([^\17]+)\17\s*>\s*(.*)|^([^<]+)(.*)/smi');

/*
#define("MINIXML_SIMPLE_REGEX",
# //         1         2      3                    4       5          6                   7          8             9          #10     11
#'/\s*<\s*([^\s>]+)([^>]+)?>(.*?)<\s*\/\\1\s*>\s*([^<]+)?(.*)|\s*<!--(.+?)-->\s*|\s*<\s*([^\s>]+)([^>]*)\/\s*>\s*([^<>]+)?|^([^<]+)(.*)/smi');

*/
define("MINIXML_SIMPLE_REGEX",'/^\s*<\s*([^\s>]+)(\s+[^>]+)?>(.*?)<\s*\/\1\s*>\s*([^<]+)?(.*)|^\s*<!--(.+?)-->\s*(.*)|^\s*<\s*([^\s>]+)([^>]+)\/\s*>\s*(.*)|^([^<]+)(.*)/smi');


// require_once(MINIXML_CLASSDIR . "/element.inc.php");

/***************************************************************************************************
****************************************************************************************************
*****
*****					  MiniXMLDoc
*****
****************************************************************************************************
***************************************************************************************************/

/* MiniXMLDoc class
**
** The MiniXMLDoc class is the programmer's handle to MiniXML functionality.
**
** A MiniXMLDoc instance is created in every program that uses MiniXML.
** With the MiniXMLDoc object, you can access the root MiniXMLElement,
** find/fetch/create elements and read in or output XML strings.
**/
class MiniXMLDoc {
	var $xxmlDoc;
	var $xuseSimpleRegex;
	var $xRegexIndex;

	/* MiniXMLDoc [XMLSTRING]
	** Constructor, create and init a MiniXMLDoc object.
	**
	** If the optional XMLSTRING is passed, the document will be initialised with
	** a call to fromString using the XMLSTRING.
	**
	*/
	function MiniXMLDoc ($string=NULL)
	{
		/* Set up the root element - note that it's name get's translated to a
		** <? xml version="1.0" ?> string.
		*/
		$this->xxmlDoc = new MiniXMLElement("PSYCHOGENIC_ROOT_ELEMENT");
		$this->xuseSimpleRegex = MINIXML_USE_SIMPLE;
		if (! is_null($string))
		{
			$this->fromString($string);
		}

	}

	function init ()
	{
		$this->xxmlDoc = new MiniXMLElement("PSYCHOGENIC_ROOT_ELEMENT");
	}

	/* getRoot
	** Returns a reference the this document's root element
	** (an instance of MiniXMLElement)
	*/
	function &getRoot ()
	{
		return $this->xxmlDoc;
	}


	/* setRoot NEWROOT
	** Set the document root to the NEWROOT MiniXMLElement object.
	**/
	function setRoot (&$root)
	{
		if ($this->isElement($root))
		{
			$this->xxmlDoc = $root;
		} else {
			return _MiniXMLError("MiniXMLDoc::setRoot(): Trying to set non-MiniXMLElement as root");
		}
	}

	/* isElement ELEMENT
	** Returns a true value if ELEMENT is an instance of MiniXMLElement,
	** false otherwise.
	*/
	function isElement (&$testme)
	{
		if (is_null($testme))
		{
			return 0;
		}

		return method_exists($testme, 'MiniXMLElement');
	}


	/* isNode NODE
	** Returns a true value if NODE is an instance of MiniXMLNode,
	** false otherwise.
	*/
	function isNode (&$testme)
	{
		if (is_null($testme))
		{
			return 0;
		}

		return method_exists($testme, 'MiniXMLNode');
	}


	/* createElement NAME [VALUE]
	** Creates a new MiniXMLElement with name NAME.
	** This element is an orphan (has no assigned parent)
	** and will be lost unless it is appended (MiniXMLElement::appendChild())
	** to an element at some point.
	**
	** If the optional VALUE (string or numeric) parameter is passed,
	** the new element's text/numeric content will be set using VALUE.
	**
	** Returns a reference to the newly created element (use the =& operator)
	*/
	function &createElement ($name=NULL, $value=NULL)
	{
		$newElement = new MiniXMLElement($name);

		if (! is_null($value))
		{
			if (is_numeric($value))
			{
				$newElement->numeric($value);
			} elseif (is_string($value))
			{
				$newElement->text($value);
			}
		}

		return $newElement;
	}

	/* getElement NAME
	** Searches the document for an element with name NAME.
	**
	** Returns a reference to the first MiniXMLElement with name NAME,
	** if found, NULL otherwise.
	**
	** NOTE: The search is performed like this, returning the first
	** 	 element that matches:
	**
	** - Check the Root Element's immediate children (in order) for a match.
	** - Ask each immediate child (in order) to MiniXMLElement::getElement()
	**  (each child will then proceed similarly, checking all it's immediate
	**   children in order and then asking them to getElement())
	*/
	function &getElement ($name)
	{

		$element = $this->xxmlDoc->getElement($name);
		if (MINIXML_DEBUG > 0)
		{
			_MiniXMLLog("MiniXMLDoc::getElement(): Returning element $element");
		}

		return $element;

	}


	/* getElementByPath PATH
	** Attempts to return a reference to the (first) element at PATH
	** where PATH is the path in the structure from the root element to
	** the requested element.
	**
	** For example, in the document represented by:
	**
	**	 <partRateRequest>
	**	  <vendor>
	**	   <accessid user="myusername" password="mypassword" />
	**	  </vendor>
	**	  <partList>
	**	   <partNum>
	**	    DA42
	**	   </partNum>
	**	   <partNum>
	**	    D99983FFF
	**	   </partNum>
	**	   <partNum>
	**	    ss-839uent
	**	   </partNum>
	**	  </partList>
	**	 </partRateRequest>
	**
	** 	$accessid =& $xmlDocument->getElementByPath('partRateRequest/vendor/accessid');
	**
	** Will return what you expect (the accessid element with attributes user = "myusername"
	** and password = "mypassword").
	**
	** BUT be careful:
	**	$accessid =& $xmlDocument->getElementByPath('partRateRequest/partList/partNum');
	**
	** will return the partNum element with the value "DA42".  Other partNums are
	** inaccessible by getElementByPath() - Use MiniXMLElement::getAllChildren() instead.
	**
	** Returns the MiniXMLElement reference if found, NULL otherwise.
	*/
	function &getElementByPath ($path)
	{

		$element = $this->xxmlDoc->getElementByPath($path);
		if (MINIXML_DEBUG > 0)
		{
			_MiniXMLLog("Returning element $element");
		}

		return $element;

	}

	function fromFile ($filename)
	{
		$modified = stat($filename);
		if (! is_array($modified))
		{
			_MiniXMLError("Can't stat '$filename'");
			return NULL;
		}

		if (MINIXML_USEFROMFILECACHING > 0)
		{

			$tmpName = MINIXML_FROMFILECACHEDIR . '/' . 'minixml-' . md5($filename);
			if (MINIXML_DEBUG > 0)
			{
					_MiniXMLLog("Trying to open cach file $tmpName (for '$filename')");
			}
			$cacheFileStat = stat($tmpName);

			if (is_array($cacheFileStat) && $cacheFileStat[9] > $modified[9])
			{

				$fp = @fopen($tmpName,"r");
				if ($fp)
				{
					if (MINIXML_DEBUG > 0)
					{
						_MiniXMLLog("Reading file '$filename' from object cache instead ($tmpName)");
					}
					$tmpFileSize = filesize($tmpName);
					$tmpFileContents = fread($fp, $tmpFileSize);

					$serializedObj = unserialize($tmpFileContents);

					$sRoot =& $serializedObj->getRoot();
					if ($sRoot)
					{
						if (MINIXML_DEBUG > 0)
						{
							_MiniXMLLog("Restoring object from cache file $tmpName");
						}
						$this->setRoot($sRoot);

						/* Return immediately, such that we don't refresh the cache */
						return $this->xxmlDoc->numChildren();

					} /* end if we got a root element from unserialized object */

				} /* end if we sucessfully opened the file */


			} /* end if cache file exists and is more recent */
		}


		ob_start();
		readfile($filename);
		$filecontents = ob_get_contents();
		ob_end_clean();

		$retVal = $this->fromString($filecontents);

		if (MINIXML_USEFROMFILECACHING > 0)
		{
			$this->saveToCache($filename);
		}

		return $retVal;


	}

	function saveToCache ($filename)
	{
		$tmpName = MINIXML_FROMFILECACHEDIR . '/' . 'minixml-' . md5($filename);

		$fp = @fopen($tmpName, "w");

		if (MINIXML_DEBUG > 0)
		{
			_MiniXMLLog("Saving object to cache as '$tmpName'");
		}

		if ($fp)
		{

			$serialized = serialize($this);
			fwrite($fp, $serialized);

			fclose($fp);
		} else {
			_MiniXMLError("Could not open $tmpName for write in MiniXMLDoc::saveToCache()");
		}

	}

	/* fromString XMLSTRING
	**
	** Initialise the MiniXMLDoc (and it's root MiniXMLElement) using the
	** XML string XMLSTRING.
	**
	** Returns the number of immediate children the root MiniXMLElement now
	** has.
	*/
	function fromString (&$XMLString)
	{
		$useSimpleFlag = $this->xuseSimpleRegex;


		if ($this->xuseSimpleRegex || ! preg_match('/<!DOCTYPE|<!ENTITY|<!\[CDATA/smi', $XMLString))
		{
			$this->xuseSimpleRegex = 1;

			$this->xRegexIndex = array(
							'biname'	=> 1,
							'biattr'	=> 2,
							'biencl'	=> 3,
							'biendtxt'	=> 4,
							'birest'	=> 5,
							'comment'	=> 6,
							'uname'		=> 8,
							'uattr'		=> 9,
							'plaintxt'	=> 11,
							'plainrest'	=> 12
				);
			$regex = MINIXML_SIMPLE_REGEX;

		} else {

			$this->xRegexIndex = array(
							'biname'	=> 1,
							'biattr'	=> 2,
							'biencl'	=> 3,
							'biendtxt'	=> 4,
							'birest'	=> 5,
							'comment'	=> 6,
							'uname'		=> 8,
							'uattr'		=> 9,
							'cdata'		=> 11,
							'doctypedef'	=> 13,
							'doctypecont'	=> 14,
							'entityname'	=> 16,
							'entitydef'	=> 18,
							'plaintxt'	=> 20,
							'plainrest'	=> 21
				);
			$regex = MINIXML_COMPLETE_REGEX;
		}

		$this->fromSubString($this->xxmlDoc, $XMLString, $regex);

		$this->xuseSimpleRegex = $useSimpleFlag;

		return $this->xxmlDoc->numChildren();

	}


	function fromArray (&$init, $params=NULL)
	{

		$this->init();


		if (! is_array($init) )
		{

			return _MiniXMLError("MiniXMLDoc::fromArray(): Must Pass an ARRAY to initialize from");
		}

		if (! is_array($params) )
		{
			$params = array();
		}

		if ( $params["attributes"] && is_array($params["attributes"]) )
		{

			$attribs = array();
			foreach ($params["attributes"] as $attribName => $value)
			{
				if (! (array_key_exists($attribName, $attribs) && is_array($attribs[$attribName]) ) )
				{
					$attribs[$attribName] = array();
				}

				if (is_array($value))
				{
					foreach ($value as $v)
					{
						if (array_key_exists($v, $attribs[$attribName]))
						{
							$attribs[$attribName][$v]++;
						} else {
							$attribs[$attribName][$v] = 1;
						}
					}
				} else {
					if (array_key_exists($value,$attribs[$attribName]))
					{
						$attribs[$attribName][$value]++;
					} else {
						$attribs[$attribName][$value] = 1;
					}
				}
			}

			// completely replace old attributes by our optimized array
			$params["attributes"] = $attribs;
		} else {
			$params["attributes"] = array();
		}

		foreach ($init as $keyname => $value)
		{
			$sub = $this->_fromArray_getExtractSub($value);


			$this->$sub($keyname, $value, $this->xxmlDoc, $params);

		}


		return $this->xxmlDoc->numChildren();

	}

	function _fromArray_getExtractSub ($v)
	{
		// is it a string, a numerical array or an associative array?
		$sub = "_fromArray_extract";
		if (is_array($v))
		{
			if (_MiniXML_NumKeyArray($v))
			{
				// All numeric - assume it is a "straight" array
				$sub .= "ARRAY";
			} else {
				$sub .= "AssociativeARRAY";
			}

		} else {
			$sub .= "STRING";
		}


		return $sub;
	}





	function _fromArray_extractAssociativeARRAY ($name, &$value, &$parent, &$params)
	{

		$thisElement =& $parent->createChild($name);

		foreach ($value as $key => $val)
		{

			$sub = $this->_fromArray_getExtractSub($val);


			$this->$sub($key, $val, $thisElement, $params);

		}

		return;
	}

	function _fromArray_extractARRAY ($name, &$value, &$parent, &$params)
	{

		foreach ($value as $val)
		{
			$sub = $this->_fromArray_getExtractSub($val);


			$this->$sub($name, $val, $parent, $params);

		}

		return;
	}


	function _fromArray_extractSTRING ($name, $value="", &$parent, &$params)
	{

		$pname = $parent->name();

		if (
			( array_key_exists($pname, $params['attributes']) && is_array($params['attributes'][$pname])
			  && array_key_exists($name, $params['attributes'][$pname]) && $params['attributes'][$pname][$name])
		     || (
		     	  array_key_exists('-all', $params['attributes']) && is_array($params['attributes']['-all'])
			  && array_key_exists($name, $params['attributes']['-all']) && $params['attributes']['-all'][$name])
		   )
		{
			$parent->attribute($name, $value);
		} elseif ($name == '-content') {

			$parent->text($value);
		} else {
			$parent->createChild($name, $value);
		}

		return;
	}



	function time ($msg)
	{
		error_log("\nMiniXML msg '$msg', time: ". time() . "\n");
	}
	// fromSubString PARENTMINIXMLELEMENT XMLSUBSTRING
	// private method, called recursively to parse the XMLString in little sub-chunks.
	function fromSubString (&$parentElement, &$XMLString, &$regex)
	{
		//$this->time('fromSubStr');

		if (is_null($parentElement) || empty($XMLString) || preg_match('/^\s*$/', $XMLString))
		{
			return;
		}
		if (MINIXML_DEBUG > 0)
		{
			_MiniXMLLog("Called fromSubString() with parent '" . $parentElement->name() . "'\n");
		}

		$matches = array();
		if (preg_match_all(  $regex, $XMLString, $matches))
		{
			// $this->time('a match');

			$mcp = $matches;

			$numMatches = count($mcp[0]);

			for($i=0; $i < $numMatches; $i++)
			{
				if (MINIXML_DEBUG > 1)
				{
					_MiniXMLLog ("Got $numMatches CHECKING: ". $mcp[0][$i] . "\n");
				}

				$uname = $mcp[$this->xRegexIndex['uname']][$i];
				$comment = $mcp[$this->xRegexIndex['comment']][$i];
				if ($this->xuseSimpleRegex)
				{
					$cdata = NULL;
					$doctypecont = NULL;
					$entityname = NULL;

					$tailEndIndexes = array(5, 7, 10, 12);
				} else {

					$cdata = $mcp[$this->xRegexIndex['cdata']][$i];
					$doctypecont = $mcp[$this->xRegexIndex['doctypecont']][$i];
					$entityname = $mcp[$this->xRegexIndex['entityname']][$i];

					$tailEndIndexes = array(5, 7, 10, 12, 15, 19, 21);
				}

				$plaintext = $mcp[$this->xRegexIndex['plaintxt']][$i];

				// check all the 'tailend' (i.e. rest of string) matches for more content
				$moreContent = '';
				$idx = 0;
				while (empty($moreContent) && ($idx < count($tailEndIndexes)))
				{
					if (! empty($mcp[$tailEndIndexes[$idx]][$i]))
					{
						$moreContent = $mcp[$tailEndIndexes[$idx]][$i];
					}

					$idx++;
				}



				if ($uname)
				{
					// _MiniXMLLog ("Got UNARY $uname");
					$newElement =& $parentElement->createChild($uname);
					$this->_extractAttributesFromString($newElement, $mcp[$this->xRegexIndex['uattr']][$i]);

				} elseif ($comment) {
					//_MiniXMLLog ("Got comment $comment");
					$parentElement->comment($comment);

				} elseif ($cdata) {
					//_MiniXMLLog ("Got cdata $cdata");
					$newElement = new MiniXMLElementCData($cdata);
					$parentElement->appendChild($newElement);
				} elseif ($doctypecont) {
					//_MiniXMLLog ("Got doctype $doctypedef '" . $mcp[11][$i] . "'");
					$newElement = new MiniXMLElementDocType($mcp[$this->xRegexIndex['doctypedef']][$i]);
					$appendedChild =& $parentElement->appendChild($newElement);
					$this->fromSubString($appendedChild, $doctypecont, $regex);

				} elseif ($entityname ) {
					//_MiniXMLLog ("Got entity $entityname");
					$newElement = new MiniXMLElementEntity ($entityname, $mcp[$this->xRegexIndex['entitydef']][$i]);
					$parentElement->appendChild($newElement);

				} elseif ($plaintext) {

					if (! preg_match('/^\s+$/', $plaintext))
					{
						$parentElement->createNode($plaintext);
					}

				} elseif($mcp[$this->xRegexIndex['biname']]) {

					// _MiniXMLLog("Got BIN NAME: " . $mcp[$this->xRegexIndex['biname']][$i]);

					$nencl = $mcp[$this->xRegexIndex['biencl']][$i];
					$finaltxt = $mcp[$this->xRegexIndex['biendtxt']][$i];

					$newElement =& $parentElement->createChild($mcp[$this->xRegexIndex['biname']][$i]);
					$this->_extractAttributesFromString($newElement, $mcp[$this->xRegexIndex['biattr']][$i]);



					$plaintxtMatches = array();
					if (preg_match("/^\s*([^\s<][^<]*)/", $nencl, $plaintxtMatches))
					{
						$txt = $plaintxtMatches[1];
						$newElement->createNode($txt);

						$nencl = preg_replace("/^\s*([^<]+)/", "", $nencl);
					}


					if ($nencl && !preg_match('/^\s*$/', $nencl))
					{
						$this->fromSubString($newElement, $nencl, $regex);
					}

					if ($finaltxt)
					{
						$parentElement->createNode($finaltxt);
					}


				} /* end switch over type of match */

				if (! empty($moreContent))
				{
					$this->fromSubString($parentElement, $moreContent, $regex);
				}


			} /* end loop over all matches */


		} /* end if there was a match */

	} /* end method fromSubString */


	/* toString [DEPTH]
	** Converts this MiniXMLDoc object to a string and returns it.
	**
	** The optional DEPTH may be passed to set the space offset for the
	** first element.
	**
	** If the optional DEPTH is set to MINIXML_NOWHITESPACES.
	** When it is, no \n or whitespaces will be inserted in the xml string
	** (ie it will all be on a single line with no spaces between the tags.
	**
	** Returns a string of XML representing the document.
	*/
	function toString ($depth=0)
	{
		$retString = $this->xxmlDoc->toString($depth);

		if ($depth == MINIXML_NOWHITESPACES)
		{
			$xmlhead = "<?xml version=\"1.0\"\\1?>";
		} else {
			$xmlhead = "<?xml version=\"1.0\"\\1?>\n ";
		}
		$search = array("/<PSYCHOGENIC_ROOT_ELEMENT([^>]*)>\s*/smi",
				"/<\/PSYCHOGENIC_ROOT_ELEMENT>/smi");
		$replace = array($xmlhead,
				"");
		$retString = preg_replace($search, $replace, $retString);


		if (MINIXML_DEBUG > 0)
		{
			_MiniXMLLog("MiniXML::toString() Returning XML:\n$retString\n\n");
		}


		return $retString;
	}


	/* toArray
	**
	** Transforms the XML structure currently represented by the MiniXML Document object
	** into an array.
	**
	** More docs to come - for the moment, use var_dump($miniXMLDoc->toArray()) to see
	** what's going on :)
	*/

	function & toArray ()
	{

		$retVal = $this->xxmlDoc->toStructure();

		if (is_array($retVal))
		{
			return $retVal;
		}

		$retArray = array(
					'-content'	=> $retVal,
				);

		return $retArray;
	}




	/* getValue()
	** Utility function, call the root MiniXMLElement's getValue()
	*/
	function getValue ()
	{
		return $this->xxmlDoc->getValue();
	}



	/* dump
	** Debugging aid, dump returns a nicely formatted dump of the current structure of the
	** MiniXMLDoc object.
	*/
	function dump ()
	{
		return serialize($this);
	}



	// _extractAttributesFromString
	// private method for extracting and setting the attributs from a
	// ' a="b" c = "d"' string
	function _extractAttributesFromString (&$element, &$attrString)
	{

		if (! $attrString)
		{
			return NULL;
		}

		$count = 0;
		$attribs = array();
		// Set the attribs
		preg_match_all('/([^\s]+)\s*=\s*([\'"])([^\2]*?)\2/sm', $attrString, $attribs);


		for ($i = 0; $i < count($attribs[0]); $i++)
		{
			$attrname = $attribs[1][$i];
			$attrval = $attribs[3][$i];

			if ($attrname)
			{
				$element->attribute($attrname, $attrval, '');
				$count++;
			}
		}

		return $count;
	}

	/* Destructor to keep things clean -- patch by Ilya */
	function __destruct()
	{
		$this->xxmlDoc = null;
	}


}



/***************************************************************************************************
****************************************************************************************************
*****
*****					   MiniXML
*****
****************************************************************************************************
***************************************************************************************************/

/* class MiniXML (MiniXMLDoc)
**
** Avoid using me - I involve needless overhead.
**
** Utility class - this is just an name aliase for the
** MiniXMLDoc class as I keep repeating the mistake of
** trying to create
**
** $xml = new MiniXML();
**
*/
class MiniXML extends MiniXMLDoc {

	function MiniXML ()
	{
		$this->MiniXMLDoc();
	}
}




/***************************************************************************************************
****************************************************************************************************
*****
*****      MiniXML - PHP class library for generating and parsing XML.
*****
*****      Copyright (C) 2002-2005 Patrick Deegan, Psychogenic.com
*****      All rights reserved.
*****
*****      http://minixml.psychogenic.com
*****
*****   This program is free software; you can redistribute
*****   it and/or modify it under the terms of the GNU
*****   General Public License as published by the Free
*****   Software Foundation; either version 2 of the
*****   License, or (at your option) any later version.
*****
*****   This program is distributed in the hope that it will
*****   be useful, but WITHOUT ANY WARRANTY; without even
*****   the implied warranty of MERCHANTABILITY or FITNESS
*****   FOR A PARTICULAR PURPOSE.  See the GNU General
*****   Public License for more details.
*****
*****   You should have received a copy of the GNU General
*****   Public License along with this program; if not,
*****   write to the Free Software Foundation, Inc., 675
*****   Mass Ave, Cambridge, MA 02139, USA.
*****
*****
*****   You may contact the author, Pat Deegan, through the
*****   contact section at http://www.psychogenic.com
*****
*****   Much more information on using this API can be found on the
*****   official MiniXML website - http://minixml.psychogenic.com
*****	or within the Perl version (XML::Mini) available through CPAN
*****
****************************************************************************************************
***************************************************************************************************/



//require_once(MINIXML_CLASSDIR . "/treecomp.inc.php");
//require_once(MINIXML_CLASSDIR . "/node.inc.php");

/***************************************************************************************************
****************************************************************************************************
*****
*****					  MiniXMLElement
*****
****************************************************************************************************
***************************************************************************************************/


/* class MiniXMLElement (MiniXMLTreeComponent)
**
** Although the main handle to the xml document is the MiniXMLDoc object,
** much of the functionality and manipulation involves interaction with
** MiniXMLElement objects.
**
** A MiniXMLElement
** has:
** - a name
** - a list of 0 or more attributes (which have a name and a value)
** - a list of 0 or more children (MiniXMLElement or MiniXMLNode objects)
** - a parent (optional, only if MINIXML_AUTOSETPARENT > 0)
**/

class MiniXMLElement extends MiniXMLTreeComponent {


	var $xname;
	var $xattributes;
	var $xchildren;
	var $xnumChildren;
	var $xnumElementChildren;

	var $xavoidLoops = MINIXML_AVOIDLOOPS;


	/* MiniXMLElement NAME
	** Creates and inits a new MiniXMLElement
	*/
	function MiniXMLElement ($name=NULL)
	{
		$this->MiniXMLTreeComponent();
		$this->xname = NULL;
		$this->xattributes = array();
		$this->xchildren = array();
		$this->xnumChildren = 0;
		$this->xnumElementChildren = 0;
		if ($name)
		{
			$this->name($name);
		} else {
			return _MiniXMLError("MiniXMLElement Constructor: must pass a name to constructor");
		}
	} /* end method MiniXMLElement */


	/**************** Get/set methods for MiniXMLElement data *****************/


	/* name [NEWNAME]
	**
	** If a NEWNAME string is passed, the MiniXMLElement's name is set
	** to NEWNAME.
	**
	** Returns the element's name.
	*/
	function name ($setTo=NULL)
	{
		if (! is_null($setTo))
		{
			if (! is_string($setTo))
			{
				return _MiniXMLError("MiniXMLElement::name() Must pass a STRING to method to set name");
			}

			$this->xname = $setTo;
		}

		return $this->xname;

	} /* end method name */



	/* attribute NAME [SETTO [SETTOALT]]
	**
	** The attribute() method is used to get and set the
	** MiniXMLElement's attributes (ie the name/value pairs contained
	** within the tag, <tagname attrib1="value1" attrib2="value2">)
	**
	** If SETTO is passed, the attribute's value is set to SETTO.
	**
	** If the optional SETTOALT is passed and SETTO is false, the
	** attribute's value is set to SETTOALT.  This is usefull in cases
	** when you wish to set the attribute to a default value if no SETTO is
	** present, eg $myelement->attribute('href', $theHref, 'http://psychogenic.com')
	** will default to 'http://psychogenic.com'.
	**
	** Note: if the MINIXML_LOWERCASEATTRIBUTES define is > 0, all attribute names
	** will be lowercased (while setting and during retrieval)
	**
	** Returns the value associated with attribute NAME.
	**
	*/
	function attribute ($name, $primValue=NULL, $altValue=NULL)
	{
		$value = (is_null($primValue) ? $altValue : $primValue );


		if (MINIXML_UPPERCASEATTRIBUTES > 0)
		{
			$name = strtoupper($name);
		} elseif (MINIXML_LOWERCASEATTRIBUTES > 0)
		{
			$name = strtolower($name);
		}

		if (! is_null($value))
		{

			$this->xattributes[$name] = $value;
		}

		if (! is_null($this->xattributes[$name]))
		{
			return $this->xattributes[$name];
		} else {
			return NULL;
		}

	} /* end method attribute */


	/* text [SETTO [SETTOALT]]
	**
	** The text() method is used to get or append text data to this
	** element (it is appended to the child list as a new MiniXMLNode object).
	**
	** If SETTO is passed, a new node is created, filled with SETTO
	** and appended to the list of this element's children.
	**
	** If the optional SETTOALT is passed and SETTO is false, the
	** new node's value is set to SETTOALT.  See the attribute() method
	** for an example use.
	**
	** Returns a string composed of all child MiniXMLNodes' contents.
	**
	** Note: all the children MiniXMLNodes' contents - including numeric
	** nodes are included in the return string.
	*/
	function text ($setToPrimary = NULL, $setToAlternate=NULL)
	{
		$setTo = ($setToPrimary ? $setToPrimary : $setToAlternate);

		if (! is_null($setTo))
		{
			$this->createNode($setTo);
		}

		$retString = '';

		/* Extract text from all child nodes */
		for($i=0; $i< $this->xnumChildren; $i++)
		{
			if ($this->isNode($this->xchildren[$i]))
			{
				$nodeTxt = $this->xchildren[$i]->getValue();
				if (! is_null($nodeTxt))
				{
					$retString .= "$nodeTxt ";

				} /* end if text returned */

			} /* end if this is a MiniXMLNode */

		} /* end loop over all children */

		return $retString;

	}  /* end method text */



	/* numeric [SETTO [SETTOALT]]
	**
	** The numeric() method is used to get or append numeric data to
	** this element (it is appended to the child list as a MiniXMLNode object).
	**
	** If SETTO is passed, a new node is created, filled with SETTO
	** and appended to the list of this element's children.
	**
	** If the optional SETTOALT is passed and SETTO is false, the
	** new node's value is set to SETTOALT.  See the attribute() method
	** for an example use.
	**
	** Returns a space seperated string composed all child MiniXMLNodes'
	** numeric contents.
	**
	** Note: ONLY numerical contents are included from the list of child MiniXMLNodes.
	**
	*/
	function numeric ($setToPrimary = NULL, $setToAlternate=NULL)
	{
		$setTo = (is_null($setToPrimary) ? $setToAlternate : $setToPrimary);

		if (! is_null($setTo))
		{
			$this->createNode($setTo);
		}

	} /* end method numeric */


	/* comment CONTENTS
	**
	** The comment() method allows you to add a new MiniXMLElementComment to this
	** element's list of children.
	**
	** Comments will return a <!-- CONTENTS --> string when the element's toString()
	** method is called.
	**
	** Returns a reference to the newly appended MiniXMLElementComment
	**
	*/
	function & comment ($contents)
	{
		$newEl = new MiniXMLElementComment();

		$appendedComment =& $this->appendChild($newEl);
		$appendedComment->text($contents);

		return $appendedComment;

	} /* end method comment */







	/*
	** docType DEFINITION
	**
	** Append a new <!DOCTYPE DEFINITION [ ...]> element as a child of this
	** element.
	**
	** Returns the appended DOCTYPE element. You will normally use the returned
	** element to add ENTITY elements, like

	** $newDocType =& $xmlRoot->docType('spec SYSTEM "spec.dtd"');
	** $newDocType->entity('doc.audience', 'public review and discussion');
	*/

	function & docType ($definition)
	{

		$newElement = new MiniXMLElementDocType($definition);
		$appendedElement =& $this->appendChild($newElement);

		return $appendedElement;
	}
	/*
	** entity NAME VALUE
	**
	** Append a new <!ENTITY NAME "VALUE"> element as a child of this
	** element.

	** Returns the appended ENTITY element.
	*/
	function & entity ($name,$value)
	{

		$newElement = new MiniXMLElementEntity($name, $value);
		$appendedEl =& $this->appendChild($newElement);

		return $appendedEl;
	}


	/*
	** cdata CONTENTS
	**
	** Append a new <![CDATA[ CONTENTS ]]> element as a child of this element.
	** Returns the appended CDATA element.
	**
	*/

	function & cdata ($contents)
	{
		$newElement = new MiniXMLElementCData($contents);
		$appendedChild =& $this->appendChild($newElement);

		return $appendedChild;
	}


	/* getValue
	**
	** Returns a string containing the value of all the element's
	** child MiniXMLNodes (and all the MiniXMLNodes contained within
	** it's child MiniXMLElements, recursively).
	**
	** Note: the seperator parameter remains officially undocumented
	** since I'm not sure it will remain part of the API
	*/
	function getValue ($seperator=' ')
	{
		$retStr = '';
		$valArray = array();
		for($i=0; $i < $this->xnumChildren; $i++)
		{
			$value = $this->xchildren[$i]->getValue();
			if (! is_null($value))
			{
				array_push($valArray, $value);
			}
		}
		if (count($valArray))
		{
			$retStr = implode($seperator, $valArray);
		}
		return $retStr;

	} /* end method getValue */



	/* getElement NAME
	** Searches the element and it's children for an element with name NAME.
	**
	** Returns a reference to the first MiniXMLElement with name NAME,
	** if found, NULL otherwise.
	**
	** NOTE: The search is performed like this, returning the first
	** 	 element that matches:
	**
	** - Check this element for a match
	** - Check this element's immediate children (in order) for a match.
	** - Ask each immediate child (in order) to MiniXMLElement::getElement()
	**  (each child will then proceed similarly, checking all it's immediate
	**   children in order and then asking them to getElement())
	*/
	function &getElement ($name)
	{

		if (MINIXML_DEBUG > 0)
		{
			$elname = $this->name();
			_MiniXMLLog("MiniXMLElement::getElement() called for $name on $elname.");
		}
		if (is_null($name))
		{
			return _MiniXMLError("MiniXMLElement::getElement() Must Pass Element name.");
		}


		/** Must only check children as checking $this results in an inability to
		*** fetch nested objects with the same name
		*** <tag>
		***  <nested>
		***   <nested>
		***     Can't get here from tag or from the first 'nested'
		***   </nested>
		***  </nested>
		*** </tag>
		if (MINIXML_CASESENSITIVE > 0)
		{
			if (strcmp($this->xname, $name) == 0)
			{
				/* This element is it * /
				return $this;
			}
		} else {

			if (strcasecmp($this->xname,$name) == 0)
			{
				return $this;
			}
		}

		***** end commented out section ****
		*/

		if (! $this->xnumChildren )
		{
			/* Not match here and and no kids - not found... */
			return NULL;
		}

		/* Try each child (immediate children take priority) */
		for ($i = 0; $i < $this->xnumChildren; $i++)
		{
			$childname = $this->xchildren[$i]->name();
			if ($childname)
			{
				if (MINIXML_CASESENSITIVE > 0)
				{
					/* case sensitive matches only */
					if (strcmp($name, $childname) == 0)
					{
						return $this->xchildren[$i];
					}
				} else {
					/* case INsensitive matching */
					if (strcasecmp($name, $childname) == 0)
					{
						return $this->xchildren[$i];
					}
				} /* end if case sensitive */
			} /* end if child has a name */

		} /* end loop over all my children */

		/* Use beautiful recursion, daniel san */
		for ($i = 0; $i < $this->xnumChildren; $i++)
		{
			$theelement =& $this->xchildren[$i]->getElement($name);
			if ($theelement)
			{
				if (MINIXML_DEBUG > 0)
				{
					_MiniXMLLog("MiniXMLElement::getElement() returning element $theelement");
				}
				return $theelement;
			}
		}

		/* Not found */
		return NULL;


	}  /* end method getElement */


	/* getElementByPath PATH
	** Attempts to return a reference to the (first) element at PATH
	** where PATH is the path in the structure (relative to this element) to
	** the requested element.
	**
	** For example, in the document represented by:
	**
	**	 <partRateRequest>
	**	  <vendor>
	**	   <accessid user="myusername" password="mypassword" />
	**	  </vendor>
	**	  <partList>
	**	   <partNum>
	**	    DA42
	**	   </partNum>
	**	   <partNum>
	**	    D99983FFF
	**	   </partNum>
	**	   <partNum>
	**	    ss-839uent
	**	   </partNum>
	**	  </partList>
	**	 </partRateRequest>
	**
	**	$partRate =& $xmlDocument->getElement('partRateRequest');
	**
	** 	$accessid =& $partRate->getElementByPath('vendor/accessid');
	**
	** Will return what you expect (the accessid element with attributes user = "myusername"
	** and password = "mypassword").
	**
	** BUT be careful:
	**	$accessid =& $partRate->getElementByPath('partList/partNum');
	**
	** will return the partNum element with the value "DA42".  Other partNums are
	** inaccessible by getElementByPath() - Use MiniXMLElement::getAllChildren() instead.
	**
	** Returns the MiniXMLElement reference if found, NULL otherwise.
	*/
	function &getElementByPath($path)
	{
		$names = split ("/", $path);

		$element = $this;
		foreach ($names as $elementName)
		{
			if ($element && $elementName) /* Make sure we didn't hit a dead end and that we have a name*/
			{
				/* Ask this element to get the next child in path */
				$element =& $element->getElement($elementName);
			}
		}

		return $element;

	} /* end method getElementByPath */



	/* numChildren [NAMED]
	**
	** Returns the number of immediate children for this element
	**
	** If the optional NAMED parameter is passed, returns only the
	** number of immediate children named NAMED.
	*/
	function numChildren ($named=NULL)
	{
		if (is_null($named))
		{
			return $this->xnumElementChildren;
		}

		/* We require only children named '$named' */
		$allkids =& $this->getAllChildren($named);

		return count($allkids);


	}


	/* getAllChildren [NAME]
	**
	** Returns a reference to an array of all this element's MiniXMLElement children
	**
	** Note: although the MiniXMLElement may contain MiniXMLNodes as children, these are
	** not part of the returned list.
	**/
	function &getAllChildren ($name=NULL)
	{
		$retArray = array();
		$count = 0;

		if (is_null($name))
		{
			/* Return all element children */
			for($i=0; $i < $this->xnumChildren; $i++)
			{
				if (method_exists($this->xchildren[$i], 'MiniXMLElement'))
				{
					$retArray[$count++] =& $this->xchildren[$i];
				}
			}
		} else {
			/* Return only element children with name $name */

			for($i=0; $i < $this->xnumChildren; $i++)
			{
				if (method_exists($this->xchildren[$i], 'MiniXMLElement'))
				{
					if (MINIXML_CASESENSITIVE > 0)
					{
						if ($this->xchildren[$i]->name() == $name)
						{
							$retArray[$count++] =& $this->xchildren[$i];
						}
					} else {
						if (strcasecmp($this->xchildren[$i]->name(), $name) == 0)
						{
							$retArray[$count++] =& $this->xchildren[$i];
						}
					} /* end if case sensitive */

				} /* end if child is a MiniXMLElement object */

			} /* end loop over all children */

		} /* end if specific name was requested */

		return $retArray;

	} /* end method getAllChildren */



	function &insertChild (&$child, $idx=0)
	{



		if (! $this->_validateChild($child))
		{
			return;
		}

		/* Set the parent for the child element to this element if
		** avoidLoops or MINIXML_AUTOSETPARENT is set
		*/
		if ($this->xavoidLoops || (MINIXML_AUTOSETPARENT > 0) )
		{
			if ($this->xparent == $child)
			{

				$cname = $child->name();
				return _MiniXMLError("MiniXMLElement::insertChild() Tryng to append parent $cname as child of "
							. $this->xname );
			}
			$child->parent($this);
		}


		$nextIdx = $this->xnumChildren;
		$lastIdx = $nextIdx - 1;
		if ($idx > $lastIdx)
		{

			if ($idx > $nextIdx)
			{
				$idx = $lastIdx + 1;
			}
			$this->xchildren[$idx] = $child;
			$this->xnumChildren++;
			if ($this->isElement($child))
			{
				$this->xnumElementChildren++;
			}

		} elseif ($idx >= 0)
		{

			$removed = array_splice($this->xchildren, $idx);
			array_push($this->xchildren, $child);
			$numRemoved = count($removed);

			for($i=0; $i<$numRemoved; $i++)
			{

				array_push($this->xchildren, $removed[$i]);
			}
			$this->xnumChildren++;
			if ($this->isElement($child))
			{
				$this->xnumElementChildren++;
			}


		} else {
			$revIdx = (-1 * $idx) % $this->xnumChildren;
			$newIdx = $this->xnumChildren - $revIdx;

			if ($newIdx < 0)
			{
				return _MiniXMLError("Element::insertChild() Ended up with a negative index? ($newIdx)");
			}

			return $this->insertChild($child, $newIdx);
		}

		return $child;
	}


	/* appendChild CHILDELEMENT
	**
	** appendChild is used to append an existing MiniXMLElement object to
	** this element's list.
	**
	** Returns a reference to the appended child element.
	**
	** NOTE: Be careful not to create loops in the hierarchy, eg
	** $parent->appendChild($child);
	** $child->appendChild($subChild);
	** $subChild->appendChild($parent);
	**
	** If you want to be sure to avoid loops, set the MINIXML_AVOIDLOOPS define
	** to 1 or use the avoidLoops() method (will apply to all children added with createChild())
	*/
	function &appendChild (&$child)
	{

		if (! $this->_validateChild($child))
		{
			_MiniXMLLog("MiniXMLElement::appendChild() Could not validate child, aborting append");
			return NULL;
		}

		/* Set the parent for the child element to this element if
		** avoidLoops or MINIXML_AUTOSETPARENT is set
		*/
		if ($this->xavoidLoops || (MINIXML_AUTOSETPARENT > 0) )
		{
			if ($this->xparent == $child)
			{

				$cname = $child->name();
				return _MiniXMLError("MiniXMLElement::appendChild() Tryng to append parent $cname as child of "
							. $this->xname );
			}
			$child->parent($this);
		}


		$this->xnumElementChildren++; /* Note that we're addind a MiniXMLElement child */

		/* Add the child to the list */
		$idx = $this->xnumChildren++;
		$this->xchildren[$idx] =& $child;

		return $this->xchildren[$idx];

	} /* end method appendChild */


	/* prependChild CHILDELEMENT
	**
	** prependChild is used to prepend an existing MiniXMLElement object to
	** this element's list.  The child will be positioned at the begining of
	** the elements child list, thus it will be output first in the resulting XML.
	**
	** Returns a reference to the prepended child element.
	*/
	function &prependChild ($child)
	{


		if (! $this->_validateChild($child))
		{
			_MiniXMLLog("MiniXMLElement::prependChild - Could not validate child, aborting.");
			return NULL;
		}

		/* Set the parent for the child element to this element if
		** avoidLoops or MINIXML_AUTOSETPARENT is set
		*/
		if ($this->xavoidLoops || (MINIXML_AUTOSETPARENT > 0) )
		{
			if ($this->xparent == $child)
			{

				$cname = $child->name();
				return _MiniXMLError("MiniXMLElement::prependChild() Tryng to append parent $cname as child of "
							. $this->xname );
			}
			$child->parent($this);
		}


		$this->xnumElementChildren++; /* Note that we're adding a MiniXMLElement child */

		/* Add the child to the list */
		$idx = $this->xnumChildren++;
		array_unshift($this->xchildren, $child);
		return $this->xchildren[0];

	} /* end method prependChild */

	function _validateChild (&$child)
	{

		if (is_null($child))
		{
			return  _MiniXMLError("MiniXMLElement::_validateChild() need to pass a non-NULL MiniXMLElement child.");
		}

		if (! method_exists($child, 'MiniXMLElement'))
		{
			return _MiniXMLError("MiniXMLElement::_validateChild() must pass a MiniXMLElement object to _validateChild.");
		}

		/* Make sure element is named */
		$cname = $child->name();
		if (is_null($cname))
		{
			_MiniXMLLog("MiniXMLElement::_validateChild() children must be named");
			return 0;
		}


		/* Check for loops */
		if ($child == $this)
		{
			_MiniXMLLog("MiniXMLElement::_validateChild() Trying to append self as own child!");
			return 0;
		} elseif ( $this->xavoidLoops && $child->parent())
		{
			_MiniXMLLog("MiniXMLElement::_validateChild() Trying to append a child ($cname) that already has a parent set "
						. "while avoidLoops is on - aborting");
			return 0;
		}

		return 1;
	}
	/* createChild ELEMENTNAME [VALUE]
	**
	** Creates a new MiniXMLElement instance and appends it to the list
	** of this element's children.
	** The new child element's name is set to ELEMENTNAME.
	**
	** If the optional VALUE (string or numeric) parameter is passed,
	** the new element's text/numeric content will be set using VALUE.
	**
	** Returns a reference to the new child element
	**
	** Note: don't forget to use the =& (reference assignment) operator
	** when calling createChild:
	**
	**	$newChild =& $myElement->createChild('newChildName');
	**
	*/
	function & createChild ($name, $value=NULL)
	{
		if (! $name)
		{
			return _MiniXMLError("MiniXMLElement::createChild() Must pass a NAME to createChild.");
		}

		if (! is_string($name))
		{
			return _MiniXMLError("MiniXMLElement::createChild() Name of child must be a STRING");
		}

		$child = new MiniXMLElement($name);

		$appendedChild =& $this->appendChild($child);

		if (! $appendedChild )
		{
			_MiniXMLLog("MiniXMLElement::createChild() '$name' child NOT appended.");
			return NULL;
		}

		if (! is_null($value))
		{
			if (is_numeric($value))
			{
				$appendedChild->numeric($value);
			} elseif (is_string($value))
			{
				$appendedChild->text($value);
			}
		}

		$appendedChild->avoidLoops($this->xavoidLoops);

		return $appendedChild;

	} /* end method createChild */



	/* removeChild CHILD
	** Removes CHILD from this element's list of children.
	**
	** Returns the removed child, if found, NULL otherwise.
	*/

	function &removeChild (&$child)
	{
		if (! $this->xnumChildren)
		{
			if (MINIXML_DEBUG > 0)
			{
				_MiniXMLLog("Element::removeChild() called for element without any children.") ;
			}
			return NULL;
		}

		$foundChild = NULL;
		$idx = 0;
		while ($idx < $this->xnumChildren && ! $foundChild)
		{
			if ($this->xchildren[$idx] == $child)
			{
				$foundChild =& $this->xchildren[$idx];
			} else {
				$idx++;
			}
		}

		if (! $foundChild)
		{
			if (MINIXML_DEBUG > 0)
			{
				_MiniXMLLog("Element::removeChild() No matching child found.") ;
			}
			return NULL;
		}

		array_splice($this->xchildren, $idx, 1);

		$this->xnumChildren--;
		if ($this->isElement($foundChild))
		{
			$this->xnumElementChildren--;
		}

		unset ($foundChild->xparent) ;
		return $foundChild;
	}


	/* removeAllChildren
	** Removes all children of this element.
	**
	** Returns an array of the removed children (which may be empty)
	*/
	function &removeAllChildren ()
	{
		$emptyArray = array();

		if (! $this->xnumChildren)
		{
			return $emptyArray;
		}

		$retList =& $this->xchildren;

		$idx = 0;
		while ($idx < $this->xnumChildren)
		{
			unset ($retList[$idx++]->xparent);
		}

		$this->xchildren = array();
		$this->xnumElementChildren = 0;
		$this->xnumChildren = 0;


		return $retList;
	}


	function & remove ()
	{
		$parent =& $this->parent();

		if (!$parent)
		{
			_MiniXMLLog("XML::Mini::Element::remove() called for element with no parent set.  Aborting.");
			return NULL;
		}

		$removed =& $parent->removeChild($this);

		return $removed;
	}



	/* parent NEWPARENT
	**
	** The parent() method is used to get/set the element's parent.
	**
	** If the NEWPARENT parameter is passed, sets the parent to NEWPARENT
	** (NEWPARENT must be an instance of MiniXMLElement)
	**
	** Returns a reference to the parent MiniXMLElement if set, NULL otherwise.
	**
	** Note: This method is mainly used internally and you wouldn't normally need
	** to use it.
	** It get's called on element appends when MINIXML_AUTOSETPARENT or
	** MINIXML_AVOIDLOOPS or avoidLoops() > 1
	**
	*/
	function &parent (&$setParent)
	{
		if (! is_null($setParent))
		{
			/* Parents can only be MiniXMLElement objects */
			if (! $this->isElement($setParent))
			{
				return _MiniXMLError("MiniXMLElement::parent(): Must pass an instance of MiniXMLElement to set.");
			}
			$this->xparent = $setParent;
		}

		return $this->xparent;

	} /* end method parent */


	/* avoidLoops SETTO
	**
	** The avoidLoops() method is used to get or set the avoidLoops flag for this element.
	**
	** When avoidLoops is true, children with parents already set can NOT be appended to any
	** other elements.  This is overkill but it is a quick and easy way to avoid infinite loops
	** in the heirarchy.
	**
	** The avoidLoops default behavior is configured with the MINIXML_AVOIDLOOPS define but can be
	** set on individual elements (and automagically all the element's children) with the
	** avoidLoops() method.
	**
	** Returns the current value of the avoidLoops flag for the element.
	**
	*/
	function avoidLoops ($setTo = NULL)
	{
		if (! is_null($setTo))
		{
			$this->xavoidLoops = $setTo;
		}

		return $this->xavoidLoops;
	}


	/* toString [SPACEOFFSET]
	**
	** toString returns an XML string based on the element's attributes,
	** and content (recursively doing the same for all children)
	**
	** The optional SPACEOFFSET parameter sets the number of spaces to use
	** after newlines for elements at this level (adding 1 space per level in
	** depth).  SPACEOFFSET defaults to 0.
	**
	** If SPACEOFFSET is passed as MINIXML_NOWHITESPACES.
	** no \n or whitespaces will be inserted in the xml string
	** (ie it will all be on a single line with no spaces between the tags.
	**
	** Returns the XML string.
	**
	**
	** Note: Since the toString() method recurses into child elements and because
	** of the MINIXML_NOWHITESPACES and our desire to avoid testing for this value
	** on every element (as it does not change), here we split up the toString method
	** into 2 subs: toStringWithWhiteSpaces(DEPTH) and toStringNoWhiteSpaces().
	**
	** Each of these methods, which are to be considered private (?), in turn recurses
	** calling the appropriate With/No WhiteSpaces toString on it's children - thereby
	** avoiding the test on SPACEOFFSET
	*/

	function toString ($depth=0)
	{
		if ($depth == MINIXML_NOWHITESPACES)
		{
			return $this->toStringNoWhiteSpaces();
		} else {
			return $this->toStringWithWhiteSpaces($depth);
		}
	}

	function toStringWithWhiteSpaces ($depth=0)
	{
		$attribString = '';
		$elementName = $this->xname;
		$spaces = $this->_spaceStr($depth) ;

		$retString = "$spaces<$elementName";


		foreach ($this->xattributes as $attrname => $attrvalue)
		{
			$attribString .= "$attrname=\"$attrvalue\" ";
		}


		if ($attribString)
		{
			$attribString = rtrim($attribString);
			$retString .= " $attribString";
		}

		if (! $this->xnumChildren)
		{
			/* No kids -> no sub-elements, no text, nothing - consider a <unary/> element */
			$retString .= " />\n";

			return $retString;
		}



		/* If we've gotten this far, the element has
		** kids or text - consider a <binary>otherstuff</binary> element
		*/

		$onlyTxtChild = 0;
		if ($this->xnumChildren == 1 && ! $this->xnumElementChildren)
		{
			$onlyTxtChild = 1;
		}



		if ($onlyTxtChild)
		{
			$nextDepth = 0;
			$retString .= "> ";
		} else {
			$nextDepth = $depth+1;
			$retString .= ">\n";
		}



		for ($i=0; $i < $this->xnumChildren ; $i++)
		{
			if (method_exists($this->xchildren[$i], 'toStringWithWhiteSpaces') )
			{

				$newStr = $this->xchildren[$i]->toStringWithWhiteSpaces($nextDepth);


				if (! is_null($newStr))
				{
					if (! ( preg_match("/\n\$/", $newStr) || $onlyTxtChild) )
					{
						$newStr .= "\n";
					}

					$retString .= $newStr;
				}

			} else {
				_MiniXMLLog("Invalid child found in $elementName ". $this->xchildren[$i]->name() );

			} /* end if has a toString method */

		} /* end loop over all children */

		/* add the indented closing tag */
		if ($onlyTxtChild)
		{
			$retString .= " </$elementName>\n";
		} else {
			$retString .= "$spaces</$elementName>\n";
		}
		return $retString;

	} /* end method toString */




	function toStringNoWhiteSpaces ()
	{
		$retString = '';
		$attribString = '';
		$elementName = $this->xname;

		foreach ($this->xattributes as $attrname => $attrvalue)
		{
			$attribString .= "$attrname=\"$attrvalue\" ";
		}

		$retString = "<$elementName";


		if ($attribString)
		{
			$attribString = rtrim($attribString);
			$retString .= " $attribString";
		}

		if (! $this->xnumChildren)
		{
			/* No kids -> no sub-elements, no text, nothing - consider a <unary/> element */

			$retString .= " />";
			return $retString;
		}


		/* If we've gotten this far, the element has
		** kids or text - consider a <binary>otherstuff</binary> element
		*/
		$retString .= ">";

		/* Loop over all kids, getting associated strings */
		for ($i=0; $i < $this->xnumChildren ; $i++)
		{
			if (method_exists($this->xchildren[$i], 'toStringNoWhiteSpaces') )
			{
				$newStr = $this->xchildren[$i]->toStringNoWhiteSpaces();

				if (! is_null($newStr))
				{
					$retString .= $newStr;
				}

			} else {
				_MiniXMLLog("Invalid child found in $elementName");

			} /* end if has a toString method */

		} /* end loop over all children */

		/* add the indented closing tag */
		$retString .= "</$elementName>";

		return $retString;

	} /* end method toStringNoWhiteSpaces */


	/* toStructure
	**
	** Converts an element to a structure - either an array or a simple string.
	**
	** This method is used by MiniXML documents to perform their toArray() magic.
	*/
	function & toStructure ()
	{

		$retHash = array();
		$contents = "";
		$numAdded = 0;



		for($i=0; $i< $this->xnumChildren; $i++)
		{
			if ($this->isElement($this->xchildren[$i]))
			{
				$name = $this->xchildren[$i]->name();

				if (array_key_exists($name, $retHash))

				{
					if (! (is_array($retHash[$name]) && array_key_exists('_num', $retHash[$name])) )
					{
						$retHash[$name] = array($retHash[$name],
									 $this->xchildren[$i]->toStructure());

						$retHash[$name]['_num'] = 2;
					} else {
						array_push($retHash[$name], $this->xchildren[$i]->toStructure() );

						$retHash[$name]['_num']++;
					}
				} else {
					$retHash[$name] = $this->xchildren[$i]->toStructure();
				}

				$numAdded++;
			} else {
				$contents .= $this->xchildren[$i]->getValue();
			}


		}


		foreach ($this->xattributes as $attrname => $attrvalue)
		{
			#array_push($retHash, array($attrname => $attrvalue));
			$retHash["_attributes"][$attrname] = $attrvalue;
			$numAdded++;
		}


		if ($numAdded)
		{
			if (! empty($contents))
			{
				$retHash['_content'] = $contents;
			}

			return $retHash;
		} else {
			return $contents;
		}

	} // end toStructure() method





	/* isElement ELEMENT
	** Returns a true value if ELEMENT is an instance of MiniXMLElement,
	** false otherwise.
	**
	** Note: Used internally.
	*/
	function isElement (&$testme)
	{
		if (is_null($testme))
		{
			return 0;
		}

		return method_exists($testme, 'MiniXMLElement');
	}


	/* isNode NODE
	** Returns a true value if NODE is an instance of MiniXMLNode,
	** false otherwise.
	**
	** Note: used internally.
	*/
	function isNode (&$testme)
	{
		if (is_null($testme))
		{
			return 0;
		}

		return method_exists($testme, 'MiniXMLNode');
	}


	/* createNode NODEVALUE [ESCAPEENTITIES]
	**
	** Private (?)
	**
	** Creates a new MiniXMLNode instance and appends it to the list
	** of this element's children.
	** The new child node's value is set to NODEVALUE.
	**
	** Returns a reference to the new child node.
	**
	** Note: You don't need to use this method normally - it is used
	** internally when appending text() and such data.
	**
	*/
	function & createNode (&$value, $escapeEntities=NULL)
	{

		$newNode = new MiniXMLNode($value, $escapeEntities);

		$appendedNode =& $this->appendNode($newNode);

		return $appendedNode;
	}


	/* appendNode CHILDNODE
	**
	** appendNode is used to append an existing MiniXMLNode object to
	** this element's list.
	**
	** Returns a reference to the appended child node.
	**
	**
	** Note: You don't need to use this method normally - it is used
	** internally when appending text() and such data.
	*/
	function &appendNode (&$node)
	{
		if (is_null($node))
		{
			return  _MiniXMLError("MiniXMLElement::appendNode() need to pass a non-NULL MiniXMLNode.");
		}


		if (! method_exists($node, 'MiniXMLNode'))
		{
			return _MiniXMLError("MiniXMLElement::appendNode() must pass a MiniXMLNode object to appendNode.");
		}

		if (MINIXML_AUTOSETPARENT)
		{
			if ($this->xparent == $node)
			{
				return _MiniXMLError("MiniXMLElement::appendnode() Tryng to append parent $cname as node of "
							. $this->xname );
			}
			$node->parent($this);
		}


		$idx = $this->xnumChildren++;
		$this->xchildren[$idx] = $node;

		return $this->xchildren[$idx];


	}

	/* Destructor to keep things clean -- patch by Ilya */
	function __destruct()
	{
		for ($i = 0; $i < count($this->xchildren); ++$i)
			$this->xchildren[$i]->xparent = null;
	}


} /* end MiniXMLElement class definition */






/***************************************************************************************************
****************************************************************************************************
*****
*****					  MiniXMLElementComment
*****
****************************************************************************************************
***************************************************************************************************/

/* The MiniXMLElementComment class is a specific extension of the MiniXMLElement class.
**
** It is used to create the special <!-- comment --> tags and an instance in created when calling
** $elementObject->comment('this is a comment');
**
** It's methods are the same as for MiniXMLElement - see those for documentation.
**/

class MiniXMLElementComment extends MiniXMLElement {

	function MiniXMLElementComment ($name=NULL)
	{
		$this->MiniXMLElement('!--');
	}


	function toString ($depth=0)
	{
		if ($depth == MINIXML_NOWHITESPACES)
		{
			return $this->toStringNoWhiteSpaces();
		} else {
			return $this->toStringWithWhiteSpaces($depth);
		}
	}


	function toStringWithWhiteSpaces ($depth=0)
	{

		$spaces = $this->_spaceStr($depth) ;

		$retString = "$spaces<!-- \n";

		if (! $this->xnumChildren)
		{
			/* No kids, no text - consider a <unary/> element */
			$retString .= " -->\n";

			return $retString;
		}

		/* If we get here, the element does have children... get their contents */

		$nextDepth = $depth+1;

		for ($i=0; $i < $this->xnumChildren ; $i++)
		{
			$retString .= $this->xchildren[$i]->toStringWithWhiteSpaces($nextDepth);
		}

		$retString .= "\n$spaces -->\n";


		return $retString;
	}


	function toStringNoWhiteSpaces ()
	{
		$retString = '';

		$retString = "<!-- ";

		if (! $this->xnumChildren)
		{
			/* No kids, no text - consider a <unary/> element */
			$retString .= " -->";
			return $retString;
		}


		/* If we get here, the element does have children... get their contents */
		for ($i=0; $i < $this->xnumChildren ; $i++)
		{
			$retString .= $this->xchildren[$i]->toStringNoWhiteSpaces();
		}

		$retString .= " -->";


		return $retString;
	}


}




/***************************************************************************************************
****************************************************************************************************
*****
*****					  MiniXMLElementCData
*****
****************************************************************************************************
***************************************************************************************************/

/* The MiniXMLElementCData class is a specific extension of the MiniXMLElement class.
**
** It is used to create the special <![CDATA [ data ]]> tags and an instance in created when calling
** $elementObject->cdata('data');
**
** It's methods are the same as for MiniXMLElement - see those for documentation.
**/

class MiniXMLElementCData extends MiniXMLElement {




	function MiniXMLElementCData ($contents)
	{

		$this->MiniXMLElement('CDATA');
		if (! is_null($contents))
		{
			$this->createNode($contents, 0) ;
		}
	}


	function toStringNoWhiteSpaces ()
	{
		return $this->toString(MINIXML_NOWHITESPACES);
	}

	function toStringWithWhiteSpaces ($depth=0)
	{
		return $this->toString($depth);
	}

	function toString ($depth=0)
	{
		$spaces = '';
		if ($depth != MINIXML_NOWHITESPACES)
		{
			$spaces = $this->_spaceStr($depth);
		}

		$retString = "$spaces<![CDATA[ ";

		if (! $this->xnumChildren)
		{
			$retString .= "]]>\n";
			return $retString;
		}

		for ( $i=0; $i < $this->xnumChildren; $i++)
		{
			$retString .= $this->xchildren[$i]->getValue();

		}

		$retString .= " ]]>\n";

		return $retString;
	}



}

/***************************************************************************************************
****************************************************************************************************
*****
*****					  MiniXMLElementDocType
*****
****************************************************************************************************
***************************************************************************************************/

/* The MiniXMLElementDocType class is a specific extension of the MiniXMLElement class.
**
** It is used to create the special <!DOCTYPE def [...]> tags and an instance in created when calling
** $elementObject->comment('');
**
** It's methods are the same as for MiniXMLElement - see those for documentation.
**/

class MiniXMLElementDocType extends MiniXMLElement {

	var $dtattr;

	function MiniXMLElementDocType ($attr)
	{
		$this->MiniXMLElement('DOCTYPE');
		$this->dtattr = $attr;
	}
	function toString ($depth)
	{
		if ($depth == MINIXML_NOWHITESPACES)
		{
			return $this->toStringNoWhiteSpaces();
		} else {
			return $this->toStringWithWhiteSpaces($depth);
		}
	}


	function toStringWithWhiteSpaces ($depth=0)
	{

		$spaces = $this->_spaceStr($depth);

		$retString = "$spaces<!DOCTYPE " . $this->dtattr . " [\n";

		if (! $this->xnumChildren)
		{
			$retString .= "]>\n";
			return $retString;
		}

		$nextDepth = $depth + 1;

		for ( $i=0; $i < $this->xnumChildren; $i++)
		{

			$retString .= $this->xchildren[$i]->toStringWithWhiteSpaces($nextDepth);

		}

		$retString .= "\n$spaces]>\n";

		return $retString;
	}


	function toStringNoWhiteSpaces ()
	{

		$retString = "<!DOCTYPE " . $this->dtattr . " [ ";

		if (! $this->xnumChildren)
		{
			$retString .= "]>\n";
			return $retString;
		}

		for ( $i=0; $i < $this->xnumChildren; $i++)
		{

			$retString .= $this->xchildren[$i]->toStringNoWhiteSpaces();

		}

		$retString .= " ]>\n";

		return $retString;
	}


}


/***************************************************************************************************
****************************************************************************************************
*****
*****					  MiniXMLElementEntity
*****
****************************************************************************************************
***************************************************************************************************/

/* The MiniXMLElementEntity class is a specific extension of the MiniXMLElement class.
**
** It is used to create the special <!ENTITY name "val">  tags and an instance in created when calling
** $elementObject->comment('');
**
** It's methods are the same as for MiniXMLElement - see those for documentation.
**/

class MiniXMLElementEntity extends MiniXMLElement {



	function MiniXMLElementEntity  ($name, $value=NULL)
	{

		$this->MiniXMLElement($name);

		if (! is_null ($value))
		{
			$this->createNode($value, 0);
		}

	}

	function toString ($depth = 0)
	{

		$spaces = '';
		if ($depth != MINIXML_NOWHITESPACES)
		{
			$spaces = $this->_spaceStr($depth);
		}

		$retString = "$spaces<!ENTITY " . $this->name();

		if (! $this->xnumChildren)
		{
			$retString .= ">\n";
			return $retString;
		}

		 $nextDepth = ($depth == MINIXML_NOWHITESPACES) ? MINIXML_NOWHITESPACES
										: $depth + 1;
		$retString .= '"';
		for ( $i=0; $i < $this->xnumChildren; $i++)
		{

			$retString .= $this->xchildren[$i]->toString(MINIXML_NOWHITESPACES);

		}
		$retString .= '"';
		$retString .= " >\n";

		return $retString;
	}


	function toStringNoWhiteSpaces ()
	{
		return $this->toString(MINIXML_NOWHITESPACES);
	}

	function toStringWithWhiteSpaces ($depth=0)
	{
		return $this->toString($depth);
	}


}


/***************************************************************************************************
****************************************************************************************************
*****
*****      MiniXML - PHP class library for generating and parsing XML.
*****
*****      Copyright (C) 2002-2005 Patrick Deegan, Psychogenic.com
*****      All rights reserved.
*****
*****      http://minixml.psychogenic.com
*****
*****   This program is free software; you can redistribute
*****   it and/or modify it under the terms of the GNU
*****   General Public License as published by the Free
*****   Software Foundation; either version 2 of the
*****   License, or (at your option) any later version.
*****
*****   This program is distributed in the hope that it will
*****   be useful, but WITHOUT ANY WARRANTY; without even
*****   the implied warranty of MERCHANTABILITY or FITNESS
*****   FOR A PARTICULAR PURPOSE.  See the GNU General
*****   Public License for more details.
*****
*****   You should have received a copy of the GNU General
*****   Public License along with this program; if not,
*****   write to the Free Software Foundation, Inc., 675
*****   Mass Ave, Cambridge, MA 02139, USA.
*****
*****
*****   You may contact the author, Pat Deegan, through the
*****   contact section at http://www.psychogenic.com
*****
*****   Much more information on using this API can be found on the
*****   official MiniXML website - http://minixml.psychogenic.com
*****	or within the Perl version (XML::Mini) available through CPAN
*****
****************************************************************************************************
***************************************************************************************************/




//require_once(MINIXML_CLASSDIR . "/treecomp.inc.php");

/***************************************************************************************************
****************************************************************************************************
*****
*****					  MiniXMLNode
*****
****************************************************************************************************
***************************************************************************************************/


/* class MiniXMLNode
** MiniXMLNodes are used as atomic containers for numerical and text data
** and act as leaves in the XML tree.
**
** They have no name or children.
**
** They always exist as children of MiniXMLElements.
** For example,
** <B>this text is bold</B>
** Would be represented as a MiniXMLElement named 'B' with a single
** child, a MiniXMLNode object which contains the string 'this text
** is bold'.
**
** a MiniXMLNode has
** - a parent
** - data (text OR numeric)
*/

class MiniXMLNode extends MiniXMLTreeComponent {


	var $xtext;
	var $xnumeric;

	/* MiniXMLNode [CONTENTS]
	** Constructor.  Creates a new MiniXMLNode object.
	**
	*/
	function MiniXMLNode ($value=NULL, $escapeEntities=NULL)
	{
		$this->MiniXMLTreeComponent();
		$this->xtext = NULL;
		$this->xnumeric = NULL;

		/* If we were passed a value, save it as the
		** appropriate type
		*/
		if (! is_null($value))
		{
			if (is_numeric($value))
			{
				if (MINIXML_DEBUG > 0)
				{
					_MiniXMLLog("Setting numeric value of node to '$value'");
				}

				$this->xnumeric = $value;
			} else {
				if (MINIXML_IGNOREWHITESPACES > 0)
				{
					$value = trim($value);
					$value = rtrim($value);
				}

				if (! is_null($escapeEntities))
				{
					if ($escapeEntities)
					{
						$value = htmlentities($value);
					}
				} elseif (MINIXML_AUTOESCAPE_ENTITIES > 0) {
					$value = htmlentities($value);
				}

				if (MINIXML_DEBUG > 0)
				{
					_MiniXMLLog("Setting text value of node to '$value'");
				}

				$this->xtext = $value;


			} /* end if value numeric */

		} /* end if value passed */

	} /* end MiniXMLNode constructor */

	/* getValue
	**
	** Returns the text or numeric value of this Node.
	*/
	function getValue ()
	{
		$retStr = NULL;
		if (! is_null($this->xtext) )
		{
			$retStr = $this->xtext;
		} elseif (! is_null($this->xnumeric))
		{
			$retStr = "$this->xnumeric";
		}

		if (MINIXML_DEBUG > 0)
		{
			_MiniXMLLog("MiniXMLNode::getValue returning '$retStr'");
		}

		return $retStr;
	}


	/* text [SETTO [SETTOALT]]
	**
	** The text() method is used to get or set text data for this node.
	**
	** If SETTO is passed, the node's content is set to the SETTO string.
	**
	** If the optional SETTOALT is passed and SETTO is false, the
	** node's value is set to SETTOALT.
	**
	** Returns this node's text, if set or NULL
	**
	*/
	function text ($setToPrimary = NULL, $setToAlternate=NULL)
	{
		$setTo = ($setToPrimary ? $setToPrimary : $setToAlternate);

		if (! is_null($setTo))
		{
			if (! is_null($this->xnumeric) )
			{
				return _MiniXMLError("MiniXMLNode::text() Can't set text for element with numeric set.");

			} elseif (! is_string($setTo) && ! is_numeric($setTo) ) {

				return _MiniXMLError("MiniXMLNode::text() Must pass a STRING value to set text for element ('$setTo').");
			}

			if (MINIXML_IGNOREWHITESPACES > 0)
			{
				$setTo = trim($setTo);
				$setTo = rtrim($setTo);
			}


			if (MINIXML_AUTOESCAPE_ENTITIES > 0)
			{
				$setTo = htmlentities($setTo);
			}


			if (MINIXML_DEBUG > 0)
			{
				_MiniXMLLog("Setting text value of node to '$setTo'");
			}

			$this->xtext = $setTo;

		}

		return $this->xtext;
	}

	/* numeric [SETTO [SETTOALT]]
	**
	** The numeric() method is used to get or set numerical data for this node.
	**
	** If SETTO is passed, the node's content is set to the SETTO string.
	**
	** If the optional SETTOALT is passed and SETTO is NULL, the
	** node's value is set to SETTOALT.
	**
	** Returns this node's text, if set or NULL
	**
	*/
	function numeric ($setToPrim = NULL, $setToAlt = NULL)
	{
		$setTo = is_null($setToPrim) ? $setToAlt : $setToPrim;

		if (! is_null($setTo))
		{
			if (! is_null($this->xtext)) {

				return _MiniXMLError("MiniXMLElement::numeric() Can't set numeric for element with text.");

			} elseif (! is_numeric($setTo))
			{
				return _MiniXMLError("MiniXMLElement::numeric() Must pass a NUMERIC value to set numeric for element.");
			}

			if (MINIXML_DEBUG > 0)
			{
				_MiniXMLLog("Setting numeric value of node to '$setTo'");
			}
			$this->xnumeric = $setTo;
		}

		return $this->xnumeric;
	}



	/* toString [DEPTH]
	**
	** Returns this node's contents as a string.
	**
	**
	** Note: Nodes have only a single value, no children.  It is
	** therefore pointless to use the same toString() method split as
	** in the MiniXMLElement class.
	**
	*/

	function toString ($depth=0)
	{
		if ($depth == MINIXML_NOWHITESPACES)
		{
			return $this->toStringNoWhiteSpaces();
		}

		if (MINIXML_DEBUG > 0)
		{
			_MiniXMLLog("MiniXMLNode::toString() call with depth $depth");
		}

		$spaces = $this->_spaceStr($depth);
		$retStr = $spaces;

		if (! is_null($this->xtext) )
		{
			/* a text element */
			$retStr .= $this->xtext;
		} elseif (! is_null($this->xnumeric)) {
			/* a numeric element */
			$retStr .=  $this->xnumeric;
		}

		/* indent all parts of the string correctly */
		$retStr = preg_replace("/\n\s*/sm", "\n$spaces", $retStr);

		return $retStr;
	}


	function toStringWithWhiteSpaces ($depth=0)
	{
		return $this->toString($depth);
	}

	function toStringNoWhiteSpaces ()
	{

		if (MINIXML_DEBUG > 0)
		{
			_MiniXMLLog("MiniXMLNode::toStringNoWhiteSpaces() call with depth $depth");
		}

		if (! is_null($this->xtext) )
		{
			/* a text element */
			$retStr = $this->xtext;
		} elseif (! is_null($this->xnumeric)) {
			/* a numeric element */
			$retStr =  $this->xnumeric;
		}

		return $retStr;
	}


} /* end class definition */



/***************************************************************************************************
****************************************************************************************************
*****
*****      MiniXML - PHP class library for generating and parsing XML.
*****
*****      Copyright (C) 2002-2005 Patrick Deegan, Psychogenic.com
*****      All rights reserved.
*****
*****      http://minixml.psychogenic.com
*****
*****   This program is free software; you can redistribute
*****   it and/or modify it under the terms of the GNU
*****   General Public License as published by the Free
*****   Software Foundation; either version 2 of the
*****   License, or (at your option) any later version.
*****
*****   This program is distributed in the hope that it will
*****   be useful, but WITHOUT ANY WARRANTY; without even
*****   the implied warranty of MERCHANTABILITY or FITNESS
*****   FOR A PARTICULAR PURPOSE.  See the GNU General
*****   Public License for more details.
*****
*****   You should have received a copy of the GNU General
*****   Public License along with this program; if not,
*****   write to the Free Software Foundation, Inc., 675
*****   Mass Ave, Cambridge, MA 02139, USA.
*****
*****
*****   You may contact the author, Pat Deegan, through the
*****   contact section at http://www.psychogenic.com
*****
*****   Much more information on using this API can be found on the
*****   official MiniXML website - http://minixml.psychogenic.com
*****	or within the Perl version (XML::Mini) available through CPAN
*****
****************************************************************************************************
***************************************************************************************************/



/***************************************************************************************************
****************************************************************************************************
*****
*****					  MiniXMLTreeComponent
*****
****************************************************************************************************
***************************************************************************************************/



/* MiniXMLTreeComponent class
** This class is only to be used as a base class
** for others.
**
** It presents the minimal interface we can expect
** from any component in the XML hierarchy.
**
** All methods of this base class
** simply return NULL except a little default functionality
** included in the parent() method.
**
** Warning: This class is not to be instatiated.
** Derive and override.
**
*/

class MiniXMLTreeComponent {

	var $xparent;

	/*  MiniXMLTreeComponent
	** Constructor.  Creates a new MiniXMLTreeComponent object.
	**
	*/
	function MiniXMLTreeComponent ()
	{
		$this->xparent = NULL;
	} /* end MiniXMLTreeComponent constructor */


	/* Get set function for the element name
	*/
	function name ($setTo=NULL)
	{
		return NULL;
	}

	/* Function to fetch an element */
	function & getElement ($name)
	{
		return NULL;
	}

	/* Function that returns the value of this
	component and its children */
	function getValue ()
	{
		return NULL;
	}

	/* parent NEWPARENT
	**
	** The parent() method is used to get/set the element's parent.
	**
	** If the NEWPARENT parameter is passed, sets the parent to NEWPARENT
	** (NEWPARENT must be an instance of a class derived from MiniXMLTreeComponent)
	**
	** Returns a reference to the parent MiniXMLTreeComponent if set, NULL otherwise.
	*/
	function &parent (&$setParent)
	{
		if (! is_null($setParent))
		{
			/* Parents can only be MiniXMLElement objects */
			if (! method_exists($setParent, 'MiniXMLTreeComponent'))
			{
				return _MiniXMLError("MiniXMLTreeComponent::parent(): Must pass an instance derived from "
							. "MiniXMLTreeComponent to set.");
			}
			$this->xparent = $setParent;
		}

		return $this->xparent;


	}

	/* Return a stringified version of the XML representing
	this component and all sub-components */
	function toString ($depth=0)
	{
		return NULL;
	}

	/* dump
	** Debugging aid, dump returns a nicely formatted dump of the current structure of the
	** MiniXMLTreeComponent-derived object.
	*/
	function dump ()
	{
		return var_dump($this);
	}

	/* helper class that everybody loves */
	function _spaceStr ($numSpaces)
	{
		$retStr = '';
		if ($numSpaces < 0)
		{
			return $retStr;
		}

		for($i = 0; $i < $numSpaces; $i++)
		{
			$retStr .= ' ';
		}

		return $retStr;
	}

	/* Destructor to keep things clean -- patch by Ilya */
	function __destruct()
	{
		$this->xparent = null;
	}

} /* end class definition */

?>
