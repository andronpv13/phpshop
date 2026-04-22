<?php

/* * ****************************************************************
  Projectname:   Automatic Keyword Generator
  Version:       0.2
  Author:        Ver Pangonilo <smp@itsp.info>
  Last modified: 21 July 2006
  Copyright (C): 2006 Ver Pangonilo, All Rights Reserved

 * GNU General Public License (Version 2, June 1991)
 *
 * This program is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.

  Description:
  This class can generates automatically META Keywords for your
  web pages based on the contents of your articles. This will
  eliminate the tedious process of thinking what will be the best
  keywords that suits your article. The basis of the keyword
  generation is the number of iterations any word or phrase
  occured within an article.

  This automatic keyword generator will create single words,
  two word phrase and three word phrases. Single words will be
  filtered from a common words list.

  Change Log:
  ===========
  0.2 Added user configurable parameters and commented codes
  for easier end user understanding.

 * **************************************************************** */

class autokeyword {

//declare variables  
//the site contents
    var $contents;
//the generated keywords
    var $keywords;
//minimum word length for inclusion into the single word
//metakeys
    var $wordLengthMin;
//minimum word length for inclusion into the 2 word 
//phrase metakeys
    var $word2WordPhraseLengthMin;
//minimum word length for inclusion into the 3 word 
//phrase metakeys
    var $word3WordPhraseLengthMin;
//minimum phrase length for inclusion into the 2 word 
//phrase metakeys
    var $phrase2WordLengthMin;
//minimum phrase length for inclusion into the 3 word 
//phrase metakeys
    var $phrase3WordLengthMin;

    function autokeyword($params = NULL) {
//get parameters
        $this->contents = $params['_W'];
        $this->wordLengthMin = $params['_W1'];
        $this->word2WordPhraseLengthMin = $params['_W2'];
        $this->word3WordPhraseLengthMin = $params['_W3'];
        $this->phrase2WordLengthMin = $params['_P2'];
        $this->phrase3WordLengthMin = $params['_P3'];
//parse single, two words and three words
//metakeys
        $this->keywords = $this->parse_words() . $this->parse_2words() . $this->parse_3words();
        return $this->keywords;
    }

//turn the site contents into an array
//then replace common html tags.
    function replace_chars($chars = NULL) {
//convert all characters to lower case  
        $_content = strtolower($chars);
//replace most common punctuations
//and html tags chars.
        $_content = str_replace(",", " ", $_content);
        $_content = str_replace("\n", " ", $_content);
        $_content = str_replace(")", " ", $_content);
        $_content = str_replace("(", " ", $_content);
        $_content = str_replace(".", " ", $_content);
        $_content = str_replace("'", " ", $_content);
        $_content = str_replace('"', " ", $_content);
        $_content = str_replace('<', " ", $_content);
        $_content = str_replace('>', " ", $_content);
        $_content = str_replace(';', " ", $_content);
        $_content = str_replace('!', " ", $_content);
        $_content = str_replace('?', " ", $_content);
        $_content = str_replace('/', " ", $_content);
        $_content = str_replace('-', " ", $_content);
        $_content = str_replace('_', " ", $_content);
        $_content = str_replace('[', " ", $_content);
        $_content = str_replace(']', " ", $_content);
        $_content = str_replace(':', " ", $_content);
        $_content = str_replace('+', " ", $_content);
        $_content = str_replace('=', " ", $_content);
        $_content = str_replace('#', " ", $_content);
        $_content = str_replace('$', " ", $_content);
        $_content = str_replace('¶', " ", $_content);
        $_content = str_replace('©', " ", $_content);
        $_content = str_replace('&quot;', " ", $_content);
        $_content = str_replace('&copy;', " ", $_content);
        $_content = str_replace('&gt;', " ", $_content);
        $_content = str_replace('&lt;', " ", $_content);


        return $_content;
    }

//single words META KEYWORDS
    function parse_words() {
//list of commonly used words 
// this can be edited to suit your needs
        $common = array();
//create an array out of the site contents
        $s = explode(" ", $this->replace_chars($this->contents));
//initialize array
        $k = array();
//iterate inside the array
        foreach ($s as $key => $val) {
//delete single or two letter words and
//Add it to the list if the word is not 
//contained in the common words list.
            if (strlen(trim($val)) >= $this->wordLengthMin and !in_array(trim($val), $common) and !is_numeric(trim($val)))
                $k[] = trim($val);
        }
//count the words
        $k = array_count_values($k);
//sort the words from
//highest count to the 
//lowest.
        arsort($k);
        $i = "";
        foreach ($k as $key => $val)
            $i .= $key . ", ";
//release unused variables
        unset($k);
        unset($s);

        return $i;
    }

    function parse_2words() {
//create an array out of the site contents
        $x = explode(" ", $this->replace_chars($this->contents));
//initilize array
        $y = array();

        for ($i = 0; $i < count($x) - 1; $i++) {
            //delete phrases lesser than 5 characters
            if ((strlen(trim($x[$i])) >= $this->word2WordPhraseLengthMin ) and (strlen(trim($x[$i + 1])) >= $this->word2WordPhraseLengthMin)) {
                $y[] = trim($x[$i]) . " " . trim($x[$i + 1]);
            }
        }

//count the 2 word phrases
        $y = array_count_values($y);
//sort the words from
//highest count to the 
//lowest.
        arsort($y);

        $z = "";
        foreach ($y as $key => $val)
            $z .= $key . ", ";
//release unused variables
        unset($y);
        unset($x);

        return $z;
    }

    function parse_3words() {
//create an array out of the site contents
        $a = explode(" ", $this->replace_chars($this->contents));
//initilize array
        $b = array();

        for ($i = 0; $i < count($a) - 1; $i++) {
            //delete phrases lesser than 5 characters
            if ((strlen(trim($a[$i])) >= $this->word3WordPhraseLengthMin) and (strlen(trim(@$a[$i + 1])) > $this->word3WordPhraseLengthMin) and (strlen(trim(@$a[$i + 2])) > $this->word3WordPhraseLengthMin) and (strlen(trim(@$a[$i]) . trim(@$a[$i + 1]) . trim(@$a[$i + 2])) > $this->phrase3WordLengthMin)) {
                $b[] = trim($a[$i]) . " " . trim($a[$i + 1]) . " " . trim($a[$i + 2]);
            }
        }

//count the 3 word phrases
        $b = array_count_values($b);
//sort the words from
//highest count to the 
//lowest.
        arsort($b);

        $c = "";
        foreach ($b as $key => $val)
            $c .= $key . ", ";
//release unused variables
        unset($a);
        unset($b);

        return $c;
    }

}

/**
 * Загрузчик Autokeyword
 * @param string $content содержание для генерации
 * @return string
 */
function callAutokeyword($content) {
    $return = null;

    $_data = strip_tags($content);
    $keyword = new autokeyword();
    $params['_W'] = $_data; //page content
    $params['_W1'] = 5;  //minimum length of single words
    $params['_W2'] = 4;  //minimum length of words for 2 word phrases
    $params['_W3'] = 3;  //minimum length of words for 3 word phrases
    $params['_P2'] = 12; //minimum length of 2 word phrases
    $params['_P3'] = 15; //minimum length of 3 word phrases
    $max_words = 12; // лимит
    $string = $keyword->autokeyword($params);

    // Обрезаем до 12 слов
    $words = explode(',', $string, $max_words + 1);
    array_pop($words);
    foreach ($words as $val)
        if (!empty($val))
            $return.=$val . ",";

    return substr($return, 0, -1);
}

?>