<?php

namespace Azit\Ddd\Helper;

use Illuminate\Support\Str;

class StringUtil {

    private string $text;
    private const DEFAULT_ENCODING = 'utf-8';

    /**
     * Constructor
     * @param string $text
     */
    private function __construct(string $text) {
        $this -> text = $text;
    }

    /**
     * Singleton
     * @param string|null $text
     * @return StringUtil
     */
    public static function of(string $text = null) {
        if (!isset($text)) {
            $text = '';
        }

        return new StringUtil($text);
    }

    public function lower() {
        $this->text = Str::lower($this->text);
        return $this;
    }

    public function upper() {
        $this->text = Str::upper($this->text);
        return $this;
    }

    public function slug(string $separator) {
        $this->text = Str::slug($this->text, $separator);
        return $this;
    }

    /**
     * Permite eliminar los acentos
     * @return $this
     */
    public function clearAccents(string $encoding = self::DEFAULT_ENCODING){
        // converting accents in HTML entities
        $string = htmlentities($this->text, ENT_NOQUOTES, $encoding);

        // replacing the HTML entities to extract the first letter
        // examples: "&ecute;" => "e", "&Ecute;" => "E", "à" => "a" ...
        $string = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $string);

        // replacing ligatures
        // Exemple "œ" => "oe", "Æ" => "AE"
        $string = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $string);

        // removing the remaining bits
        $this->text = preg_replace('#&[^;]+;#', '', $string);
        return $this;
    }

    /**
     * Permite realizar una limpieza de caracteres especiales y espacios raros
     * @return $this
     */
    public function clearSpecialChars(){
        $this->clearSpaces();
        $this->clearAccents();

        $string = str_replace(' ', '-', $this->text);
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        $this->text = preg_replace('/-+/', '-', $string);
        return $this;
    }

    /**
     * Elimina espacios adicionales
     * @return $this
     */
    public function clearSpaces(){
        $this->text = Str::squish($this->text);
        return $this;
    }

    public function isEmail() : bool {
        return filter_var($this -> text, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Obtiene el string limpio
     * @return string
     */
    public function toString() {
        return $this->text;
    }

}
