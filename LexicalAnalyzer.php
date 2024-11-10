<?php
class LexicalAnalyzer {

public $keywords = ['auto', 'break', 'case', 'char', 'const', 'continue', 'default', 'do', 'double', 'else', 'enum', 'extern', 'float', 
            'for', 'goto', 'if', 'int', 'long', 'register', 'return', 'short', 'signed', 'sizeof', 'static', 'struct', 'switch', 
            'typedef', 'union', 'unsigned', 'void', 'volatile', 'while'];
public $operators = ['+', '-', '*', '/', '%', '++', '--', '==', '!=', '>', '<', '>=', '<=', '&&', '||', '!', '&', '|', '^', '~', '<<', 
            '>>', '=', '+=', '-=', '*=', '/=', '%=', '&=', '|=', '^=', '<<=', '>>=', '&&=', '||='];
public $punctuation = [';', ',', '{', '}', '(', ')'];

public function isKeyword($word) {
    return in_array($word, $this->keywords);
}

public function isOperator($char) {
    return in_array($char, $this->operators);
}

public function isPunctuation($char) {
    if (in_array($char, $this->punctuation)) {
        return ['type' => 'punctuation', 'value' => $char];
    }
    return false;
}

public function isNumber($str) {
    return is_numeric($str) || preg_match('/^0x[0-9a-fA-F]+$/', $str);
}

public function isIdentifier($word) {
    if (empty($word)) {
        return false;
    }
    if ($this->isKeyword($word) || $this->isOperator($word) || $this->isPunctuation($word[0])) {
        return false;
    }
    return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $word);
}

public function addToken(&$tokens, $currentToken) {
    if ($this->isKeyword($currentToken)) {
        $tokens[] = ['type' => 'keyword', 'value' => $currentToken];
    } elseif ($this->isNumber($currentToken)) {
        $tokens[] = ['type' => 'number', 'value' => $currentToken];
    } elseif ($this->isIdentifier($currentToken)) {
        $tokens[] = ['type' => 'identifier', 'value' => $currentToken];
    } elseif ($this->isPunctuation($currentToken)) {
        if ($currentToken === ';') {
            $tokens[] = ['type' => 'semicolon', 'value' => $currentToken];
        } else {
            $tokens[] = ['type' => 'punctuation', 'value' => $currentToken];
        }
    } elseif ($this->isOperator($currentToken)) {
        $tokens[] = ['type' => 'operator', 'value' => $currentToken];
    } else {
        $tokens[] = $currentToken;
    }
}

public function tokenize($input) {
    $tokens = [];
    $currentToken = '';
    $length = strlen($input);
    $inString = false; 
    $inComment = false;

    for ($i = 0; $i < $length; $i++) {
        $char = $input[$i];

        // Handle comments
        if (!$inString && $char === '/' && $i < $length - 1 && $input[$i + 1] === '/') {
            $inComment = true;
            $i++;
            continue;
        }
        if ($inComment && $char === "\n") {
            $inComment = false;
            continue;
        }
        if ($inComment) {
            continue;
        }

        if ($char === '"') {
            if ($inString) {
                $currentToken .= $char;
                $tokens[] = ['type' => 'string_literal', 'value' => $currentToken];
                $currentToken = '';
                $inString = false;
            } else {
                if ($currentToken !== '') {
                    $tokens[] = $currentToken;
                    $currentToken = '';
                }
                $currentToken = $char;
                $inString = true;
            }
            continue;
        }
        if ($inString) {
            $currentToken .= $char;
            continue;
        }

        if (ctype_space($char)) {
            if ($currentToken !== '') {
                $this->addToken($tokens, $currentToken);
                $currentToken = '';
            }
            continue;
        }

        if ($this->isOperator($char)) {
            if ($currentToken !== '') {
                $this->addToken($tokens, $currentToken);
                $currentToken = '';
            }
            // Check for compound assignment operators
            if ($char === '+' && isset($input[$i + 1]) && $input[$i + 1] === '=') {
                $tokens[] = ['type' => 'compound_assignment_operator', 'value' => '+='];
                $i++; // Skip the next character '='
            } elseif ($char === '-' && isset($input[$i + 1]) && $input[$i + 1] === '=') {
                $tokens[] = ['type' => 'compound_assignment_operator', 'value' => '-='];
                $i++; // Skip the next character '='
            } elseif ($char === '+' && isset($input[$i + 1]) && $input[$i + 1] === '+') {
                $tokens[] = ['type' => 'increment_operator', 'value' => '++'];
                $i++; // Skip the next character '+'
            } elseif ($char === '-' && isset($input[$i + 1]) && $input[$i + 1] === '-') {
                $tokens[] = ['type' => 'decrement_operator', 'value' => '--'];
                $i++; // Skip the next character '-'
            } else {
                $tokens[] = ['type' => 'operator', 'value' => $char];
            }
            continue;
        }

        if (in_array($char, $this->punctuation)) {
            if ($currentToken !== '') {
                $this->addToken($tokens, $currentToken);
                $currentToken = '';
            }
            $this->addToken($tokens, $char);
            continue;
        }

        $currentToken .= $char;
    }

    if ($currentToken !== '') {
        if ($this->isKeyword($currentToken)) {
            $tokens[] = ['type' => 'keyword', 'value' => $currentToken];
        } elseif ($this->isNumber($currentToken)) {
            $tokens[] = ['type' => 'number', 'value' => $currentToken];
        } elseif ($this->isIdentifier($currentToken)) {
            $tokens[] = ['type' => 'identifier', 'value' => $currentToken];
        }
    }

    return $tokens;
}

}
