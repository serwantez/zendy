<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Pdf;

/**
 * Wyciąga tekst z pliku pdf
 * 
 * @link http://www.hashbangcode.com/blog/zend-lucene-and-pdf-documents-part-2-pdf-data-extraction-437.html
 */
class PdfParser {

    /**
     * Separator wierszy
     * 
     * @var string
     */
    public $brSeparator = ';';

    /**
     * Convert a PDF into text.
     *
     * @param \Zend_Pdf $pdf
     * @return array
     */
    public function pdf2txt(\Zend_Pdf $pdf) {
        $data = $pdf->render();

        /**
         * Split apart the PDF document into sections. We will address each
         * section separately.
         */
        $a_obj = $this->getDataArray($data, "obj", "endobj");
        $j = 0;

        /**
         * Attempt to extract each part of the PDF document into a "filter"
         * element and a "data" element. This can then be used to decode the
         * data.
         */
        foreach ($a_obj as $obj) {
            $a_filter = $this->getDataArray($obj, "<<", ">>");
            if (is_array($a_filter) && isset($a_filter[0])) {
                $a_chunks[$j]["filter"] = $a_filter[0];
                $a_data = $this->getDataArray($obj, "stream", "endstream");
                if (is_array($a_data) && isset($a_data[0])) {
                    $a_chunks[$j]["data"] = trim(substr($a_data[0]
                                    , strlen("stream")
                                    , strlen($a_data[0]) - strlen("stream") - strlen("endstream")));
                }
                $j++;
            }
        }

        $result_data = array();

        $ci = 0;
        // decode the chunks
        foreach ($a_chunks as $chunk) {
            // Look at each chunk decide if we can decode it by looking at the contents of the filter
            if (isset($chunk["data"])) {
                // look at the filter to find out which encoding has been used
                if (strpos($chunk["filter"], "FlateDecode") !== false) {
                    // Use gzuncompress but supress error messages.
                    $data = @ gzuncompress($chunk["data"]);
                    //$data = iconv('windows-1250', 'utf-8', $data);
                    if (trim($data) != "") {
                        // If we got data then attempt to extract it.
                        //if ($ci > 0)
                        //$result_data .= $this->chunkSeparator;
                        $result_data[] = $this->ps2txt($data);
                        $ci++;
                    } else {
                        $result_data[] = $data;
                        $ci++;
                    }
                }
            }
        }
        /**
         * Make sure we don't have large blocks of white space before and after
         * our string. Also extract alphanumerical information to reduce
         * redundant data.
         */
        //$result_data = trim(preg_replace('/([^a-z0-9 ])/i', ' ', $result_data));
        // Return the data extracted from the document.
        return $result_data;
    }

    /**
     * Strip out the text from a small chunk of data.
     *
     * @param  string $ps_data The chunk of data to convert.
     * @return array
     */
    public function ps2txt($ps_data) {
        // Stop this function returning bogus information from non-data string.
        if (ord($ps_data[0]) < 10) {
            return $ps_data;
        }
        if (substr($ps_data, 0, 8) == '/CIDInit') {
            return '';
        }

        $result = array();

        $a_data = $this->getDataArray($ps_data, "[", "]");

        // Extract the data.
        if (is_array($a_data)) {
            foreach ($a_data as $ps_text) {
                $a_text = $this->getDataArray($ps_text, "(", ")", FALSE);
                if (is_array($a_text)) {
                    foreach ($a_text as $text) {
                        $result[] = $text;
                        //. $this->dataSeparator
                    }
                }
            }
        }

        // Didn't catch anything, try a different way of extracting the data
        if (!count($result)) {
            //BT - begin text, ET - end text
            $a_text = $this->getDataArray($ps_data, "BT", "ET", FALSE);
            if (is_array($a_text)) {
                foreach ($a_text as $text) {
                    //tekst wielolinijkowy
                    $a_text_multi = $this->getDataArray($text, "(", ")", FALSE);
                    if (is_array($a_text_multi)) {
                        $text = implode($this->brSeparator, $a_text_multi);
                        $result[] = trim($text);
                        // . $this->dataSeparator
                    }
                }
            }
        }

        // Remove any stray characters left over.
        //$result = preg_replace('/\b([^a|i])\b/i', ' ', $result);
        return $result;
    }

    /**
     * Convert a section of data into an array, separated by the start and end words.
     *
     * @param  string $data       The data.
     * @param  string $start_word The start of each section of data.
     * @param  string $end_word   The end of each section of data.
     * @param  bool   $resultWithTags
     * @return array              The array of data.
     */
    public function getDataArray($data, $start_word, $end_word, $resultWithTags = true) {
        $start = 0;
        $end = 0;
        $a_result = array();

        while ($start !== false && $end !== false) {
            $start = strpos($data, $start_word, $end);
            $end = strpos($data, $end_word, $start);
            if ($end !== false && $start !== false) {
                if (!$resultWithTags) {
                    $start += strlen($start_word);
                    $end -= strlen($end_word);
                }
                // data is between start and end
                $a_result[] = substr($data, $start, $end - $start + strlen($end_word));
            }
        }

        return $a_result;
    }

}