<?php

class HTMLLISTABLETABLECLASS {

    public $name;
    public $labels;
    public $data;
    public $datadelete;

    function __construct() {
        $this->labels = array();
        $this->data = array();
        $this->datadelete = array();
    }

    function byNew($inputname) {
        $instance = new Self();
        $instance->name = $inputname;
        return $instance;
    }

    function addLabel($labelname, $selectdata = array()) {
        $this->labels[] = $labelname;
        $this->labelSelectData[] = $selectdata;
    }

    function addRow($rowdata, $deletable = false) {
        $this->data[] = $rowdata;
        $this->datadelete[] = $deletable;
    }

    function renderHTML($allowedit = true) {
        $inputname = $this->name . '[]';
        $html = '';
        $html .= '<table id="' . $this->name . '" class="datatable listtable" data-sortorder="1"><thead><tr>';
        foreach ($this->labels as $label) {
            $html .= "<td>$label</td>";
        }
        if ($allowedit) {
            $html .= '<td>Delete?</td>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($this->data as $dataset) {
            $html .= '<tr>';
            foreach ($dataset as $data) {
                $html .= "<td>" . ucwords($data) . "<input type='hidden' name='$inputname' value='$data'/></td>";
            }
            if ($allowedit) {
                $html .= '<td><button type="button">Delete</button></td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody><tfoot><tr>';
        foreach ($this->labels as $label) {
            $html .= "<td><input type='text'/></td>";
        }
        if ($allowedit) {
            $html .= '<td><button type="button">Add Row</button></td>';
        }
        $html .= '</tr></tfoot></table>';

        $html .= '<script>'
                . 'function tableSortFunction(rowNumber,sortOrder) {
                        return function(a,b) {
                            var a_val = $(a).children("td:nth-of-type("+rowNumber+")").text().toLowerCase();
                            var b_val = $(b).children("td:nth-of-type("+rowNumber+")").text().toLowerCase();
                            if (isNumber(a_val)) a_val = parseInt(a_val);
                            if (isNumber(b_val)) b_val = parseInt(b_val);
                            if (a_val>b_val){ return 1*sortOrder; }
                            if (a_val<b_val){ return -1*sortOrder; }
                            return 0;
                            };
                        }'
                . '$("#' . $this->name . ' > thead > tr > td").each(function(index,element) {
                            $(element).click(function() { 
                                var sort = parseInt($("#' . $this->name . '").data("sortorder"));
                                $(this).removeClass().addClass("selected").addClass("order"+sort).siblings().removeClass();
                                $("#' . $this->name . ' > tbody > tr").sort(tableSortFunction(index+1,sort)).appendTo("#' . $this->name . ' > tbody");
                                $("#' . $this->name . '").data("sortorder",sort*-1);
                            });
                       });';
        foreach ($this->labelSelectData as $key => $selectData) {
            if (count($selectData) > 0) {
                $html .= $this->name . 'Selector' . $key . ' = ' . json_encode($selectData) . ';' .
                        '$("#' . $this->name . ' > tfoot > tr > td:nth-child(' . ($key + 1) . ') > input").autocomplete({
                                        lookup: ' . $this->name . 'Selector' . $key . ',
                                        minChars: 2,
                                        onSelect: function(suggestion) { $(this).val(suggestion.data); }
                                    }).blur(function() {
                                        var val = $(this).val();
                                        for(var n in ' . $this->name . 'Selector' . $key . ') 
                                            if (val === ' . $this->name . 'Selector' . $key . '[n].data) 
                                                return;
                                        $(this).val("");
                                    }).on("remove",function() { 
                                        delete ' . $this->name . 'Selector' . $key . ';
                                        $(this).autocomplete().dispose();});';
            }
        }
        if ($allowedit) {
            $deleteTd = '<td><button type=\'button\' onclick=\'$(this).parent().parent().remove();\'>delete</button></td>';
        }
        $html .= '$("#' . $this->name . ' > tbody > tr > td > button").click(function() {$(this).parent().parent().remove();});';
        $html .= '$("#' . $this->name . ' > tfoot > tr > td > button").click(function() {'
                . 'var values = ""; '
                . '$("#' . $this->name . ' > tfoot > tr > td > input").each(function(index, element) { '
                . 'if ($(element).val() === "") {'
                . 'values = ""; return false;'
                . '} else {'
                . 'values += "<td>"+($(element).val().toTitleCase())+"<input type=\'hidden\' name=\'' . $this->name . '[]\' value=\'"+$(element).val()+"\'/></td>";'
                . '$(element).val("");'
                . '}'
                . '});'
                . 'if (values === "") return false;'
                . '$("#' . $this->name . ' > tbody").append("<tr>"+values+"' . $deleteTd . '</tr>");'
                . '});';
        $html .= '</script>';
        return $html;
    }

    function renderArray() {
        if (!isset($_POST[$this->name])) return array();
        $list = array();
        while (count($_POST[$this->name]) >= count($this->labels)) {
            $item = array();
            foreach ($this->labels as $label) {
                $item[$label] = array_shift($_POST[$this->name]);
            }
            $list[] = $item;
        }
        return $list;
    }

}

class HTMLCODETABLEFUNCTIONCLASS {

    public $name;
    public $codeblock;
    public $argumentCount;
    public $codeTranslation;
    public $parent;
    public $phpstringify;

    function __construct() {
        $this->codeblock = '';
        $this->argumentCount = 0;
        $this->name = '';
        $this->codeTranslation = '';
    }

    function byNew($name, $parent) {
        $instance = new self();
        $instance->name = $name;
        $instance->parent = $parent;
        $inputName = $parent->name . '[]';
        $instance->codeblock = "<input type='hidden' name='$inputName' value='$name'/>";
        return $instance;
    }

    function addText($text) {
        $this->codeblock .= $text;
        return $this;
    }

    function addTitle($text) {
        $this->codeblock .= "<h1>$text</h1>";
        return $this;
    }

    function addInput($default) {
        $name = $this->parent->name . '[]';
        $this->codeblock .= "<input type='text' name='$name' value='$default'/>";
        $this->argumentCount += 1;
        return $this;
    }

    function addInputDialog($subject) {
        $name = $this->parent->name . '[]';
        $this->codeblock .= "<input onclick='codeEditorFillInput = \$(this); inlinePopup(\"fillInput\",\"$subject\");' type='text' name='$name' value='::Click Here::'/>";
        $this->argumentCount += 1;
        return $this;
    }

    function addTextarea($default) {
        $name = $this->parent->name . '[]';
        $this->codeblock .= "<textarea name='$name'>$default</textarea>";
        $this->argumentCount += 1;
        return $this;
    }

    function addSelect($list, $defaultValue = null) {
        $name = $this->parent->name . '[]';
        $this->codeblock .= "<select name='$name'>";
        foreach ($list as $option) {
            if (is_array($option)) {
                $name = $option['name'];
                $value = $option['value'];
            } else {
                $name = $option;
                $value = $option;
            }
            $selected = (strcmp($value, $defaultValue) === 0 ? "selected='true'" : "");
            $this->codeblock .= "<option value='$value' $selected>$name</option>";
        }
        $this->codeblock .= '</select>';
        $this->argumentCount += 1;
        return $this;
    }

    function addCheckbox($defaultValue = null) {
        $name = $this->parent->name . '[]';
        if ($defaultValue !== null) {
            $defaultValue = 'checked';
        }
        $this->codeblock .= "<input type='checkbox' name='$name' $defaultValue />";
        $this->argumentCount += 1;
        return $this;
    }

    function addCodeContainer() {
        $this->codeblock .= "<div class='codeContainer'></div>";
        return $this;
    }

    function addEndIf() {
        $name = $this->parent->name . '[]';
        $this->codeblock .= "<input type='hidden' name='$name' value='endif'/><center>=========</center>";
        return $this;
    }

    function addElse() {
        $name = $this->parent->name . '[]';
        $this->codeblock .= "<input type='hidden' name='$name' value='else'/>";
        return $this;
    }

    function addCodeTranslation($function) {
        $this->phpstringify = $function;
        return $this;
    }

    function renderPHP() {
        if (!isset($this->phpstringify)) {
            return '';
        }
        $arguments = array();
        $i = 0;
        if ($this->argumentCount > 0) {
            while (true) {
                $arguments[] = array_shift($_POST[$this->parent->name]);
                $i ++;
                if ($i >= $this->argumentCount) {
                    break;
                }
            }
        }
        $function = $this->phpstringify;
        return $function($arguments);
    }

    function renderHTML() {
        
    }

}

class HTMLCODETABLECLASS {

    public $name;
    private $functionChildren;

    function __construct() {
        $this->functionChildren = array();
        $this->functionGroups = array();
    }

    function byNew($inputName) {
        $instance = new self();
        $instance->name = $inputName;
        return $instance;
    }

    function createGroup($title) {
        $this->functionChildren[] = $title;
    }

    function createFunction($name) {
        return $this->functionChildren[] = HTMLCODETABLEFUNCTIONCLASS::byNew($name, $this);
    }

    function renderHTML($defaultRawCode = '') {
        $html = '<table id="' . $this->name . '" class="codeTable"><thead><tr><td>Functions</td><td>Code</td></tr></thead><tbody><tr><td>';
        $ingroup = false;
        foreach ($this->functionChildren as $child) {
            if (is_string($child)) {
                if ($ingroup === true) {
                    $html .= '</div>';
                }
                $ingroup = true;
                $html .= '<b>' . $child . '</b>';
                $html .= '<div>';
            } else {
                $html .= '<button type="button">' . $child->name . '</button>';
                $html .= '<div>' . $child->codeblock . '</div>';
            }
        }
        if ($ingroup === true) {
            $html .= '</div>';
        }
        $html .= '<input type="hidden" name="' . $this->name . '[]" value="start"/></td><td><div class="codeContainer selected">' . $defaultRawCode . '</div></td></tr></tbody></table>';
        $html .= "<script>
                        $('#" . $this->name . ".codeTable > tbody > tr > td:first-child > b').click(function() {
                            $(this).siblings('div').hide(100);
                            $(this).next().show(100);
                        });
                        $('#" . $this->name . ".codeTable > tbody > tr > td:first-child button').click(function() {
                            var clone = $(this).next().clone(true);
                            clone.append('<a class=\"close\" href=\"javascript:\" onclick=\"$(this).parent().remove();\">X</a>');
                            clone.append('<a class=\"next\" href=\"javascript:\" onclick=\"$(this).parent().insertBefore($(this).parent().prev());\">&UpArrow;</a>');
                            clone.append('<a class=\"prev\" href=\"javascript:\" onclick=\"$(this).parent().insertAfter($(this).parent().next());\">&DownArrow;</a>');
                            clone.show().appendTo('#" . $this->name . " .codeContainer.selected').children().show();
                            return false;
                        });
                        $('#" . $this->name . ".codeTable .codeContainer').click(function(e) {
                            $('#" . $this->name . ".codeTable .codeContainer').removeClass('selected');
                            $(this).addClass('selected');
                            e.stopPropagation();
                        });
                        $('#" . $this->name . ".codeTable > tbody > tr > td:first-child div').hide();
                        $('#" . $this->name . ".codeTable').parents('form:first').submit(function() {
                            $('input').each(function() {
                                var type = $(this).attr('type');
                                if (type === 'checkbox' || type === 'radio') {
                                    $(this).attr('checked', $(this).is(':checked'));
                                } else {
                                    $(this).attr('value', $(this).val());
                                }
                            });
                            $('select').each(function() {
                                var value = $(this).val();
                                $(this).children('option').removeAttr('selected');
                                $(this).children('option[value=\"' + value + '\"]').attr('selected', 'selected');
                            });
                            $('textarea').each(function() {
                                $(this).html($(this).val());
                            });
                            $('#" . $this->name . " .selected').removeClass('selected');
                            $('<input type=\"hidden\" name=\"" . $this->name . "Raw\"/>').val($('#" . $this->name . " > tbody > tr > td > .codeContainer').html()).appendTo($(this));

                            MENU.body.reloadForm($(this));
                            return false;
                        });
                      </script>";
        return $html;
    }

    function renderPHP() {
        if (!isset($_POST[$this->name])) {
            return 'Post data does not exist for CODETABLE:' . $this->name;
        }
        $string = '/* Code Generated */ ';

        while (( strcmp(array_shift($_POST[$this->name]), 'start') !== 0)) {
            continue;
        }
        while (($functionName = array_shift($_POST[$this->name])) !== null) {
            if (strcmp($functionName, 'endif') === 0) {
                $string .= ' endif;';
            } elseif (strcmp($functionName, 'else') === 0) {
                $string .= ' else: ';
            } else {
                foreach ($this->functionChildren as $child) {
                    if (!is_string($child) && strcmp($child->name, $functionName) === 0) {
                        $string .= ' ' . $child->renderPHP();
                    }
                }
            }
        }
        return $string;
    }

    function renderRawCode() {
        if (!isset($_POST[$this->name . 'Raw'])) {
            return '';
        }
        return $_POST[$this->name . 'Raw'];
    }

}
