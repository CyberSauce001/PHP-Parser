<!-- this version of XML parser uses global variables rather classes -->
<html>
<title>CS 3500 PHP Lab </title>
<body>
<table border=1 cellpadding=2 cellspacing=0>
<tr>
   <td>Name:</td><td>Alexander Nguyen</td>
</tr>
<tr>
   <td>Course:</td><td>CS 3500: Programming Languages</td>
</tr><tr>
   <td>Lab 12:</td><td>Parsing XML with PHP</td>
</tr>
</table>
<hr>

<?php
   parse('data.xml');
   echo "<H3>Software Inventory List</H3><pre>";
   dump_array();
   report(); 
//----------------------------------------------------------------------------

// setup variables, open file, create a parser object and event handlers
function parse($filename)
{
     global $n_items;
     $n_items = 0;
     
     // the parser comes with PHP 
     $xml_parser = xml_parser_create();
     
     // set handler for start and end tags   
     xml_set_element_handler($xml_parser, "XML_startElement","XML_endElement");

     // set handler for character data between tags
     xml_set_character_data_handler($xml_parser, "XML_data");

     if( !($fp = fopen($filename, 'r')) ) {
            die("could not open XML input");
     }

     // read 4096 bytes at a time
     while($data = fread($fp, 4096))
     {
        // parse the file
        if ( !xml_parse ( $xml_parser, $data, feof($fp) ) )
        {
          die(sprintf("XML error: %s at line %d",
          xml_error_string(xml_get_error_code($xml_parser)),
          xml_get_current_line_number($xml_parser)));
        }
     }
     xml_parser_free($xml_parser);
}
//----------------------------------------------------------------------------

// specify what to do with each parsed token; i.e. this is the token handler
function XML_startElement($parser, $name, $attrs = '')
{
     global $n_items;
     global $software;
     switch($name)
     { 
        case 'SOFTWARE':
           $software = array(); 
           break;
         case 'ITEM':
           $software[ $n_items ] = array();
           foreach($attrs as $key => $value)
           {
              $software[ $n_items ][ strtolower($key) ] = $value;
           } 
           $n_items++;
           break;
      }
} 
//----------------------------------------------------------------------------

// this is the event handler for the element's character data
function XML_data($parser, $data)
{
  // $data holds the character data
  // your job is to add it to the $software array
 
    global $software;
    global $n_items;

    $trimmed = trim($data);

    if($trimmed)
    {
        $software[ $n_items - 1]['NOTES'] = $data;
    }
}

//----------------------------------------------------------------------------
// dump everything 
function dump_array() {
     global $n_items;
     global $software; 

     // a var_dump function
     echo substr(date('r'),0,16);
     print_r($software);   
     printf('number of items: %d',$n_items);
}
//----------------------------------------------------------------------------
// this function must be present even if empty
function XML_endElement($parser, $name) 
{

  echo "Hit the closing tag.<p/>"; 
}

//----------------------------------------------------------------------------

// display a tabular list of item name, item description and item count
function report()
{

    // you code this
    global $n_items;
    global $software;

    echo "\n";
    foreach($software as $key => $value)
    {
        foreach($value as $left => $right)
        {
            echo "\n", $left, " ", $right;
        }
        echo "\n";
    }
}

function findbyregex($software, $string)
{
    global $n_items;
    global $software;
    $count = 0;

    printf("\Names of matches for $s:\n".$pattern);
    $i = 0;

    while($i < $n_items)
    {
        $category = $software[$i]["category"];
        $description = $software[$i]["description"];
        $name = $software[$i]["category"];

        if(preg_match($pattern, $description))
        {
            printf("%s\n", $name);
            $i++;
            $count++;
            continue;
        }
        $i++;
    }
    printf("Records matched: %d \n", $count);
}
?>
