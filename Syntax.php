<?php
class SyntaxAnalyzer {
    private $tokens;
    private $currentToken;

    public function __construct($tokens) {
        $this->tokens = $tokens;
        $this->currentToken = reset($this->tokens);
    }

    private function consume($expectedType) {
        if ($this->currentToken !== false && $this->currentToken['type'] === $expectedType) {
            $this->currentToken = next($this->tokens);
        } else {
            $this->currentToken = next($this->tokens);
            throw new Exception("Syntax error: expected {$expectedType}, got " . ($this->currentToken !== false ? $this->currentToken['type'] : 'null'));
        }
    }
    
    
    private function parseExpression() {
        if ($this->currentToken['type'] === 'number') {
            $this->consume('number');
        } elseif ($this->currentToken['type'] === 'identifier') {
            $this->consume('identifier');
            if ($this->currentToken !== false && $this->currentToken['type'] === 'operator') {
                if ($this->currentToken['value'] === '++' || $this->currentToken['value'] === '--') {
                    // Handle increment/decrement operators
                    $this->consume('operator');
                } else {
                    // Handle other operators
                    $this->consume('operator');
                    if ($this->currentToken['type'] === 'identifier') {
                        $this->consume('identifier');
                    } else if ($this->currentToken['type'] === 'number') {
                        $this->consume('number');
                    }
                }
            }
        } else {
            throw new Exception("Syntax error: expected number or identifier, got {$this->currentToken['type']}");
        }
    }
    
    
    public function parse() {
        $this->parseProgram();
    }

    private function parseProgram() {
        while ($this->currentToken !== false) {
            $this->parseStatement();
        } 
    }

    private function parseStatement() {
        if ($this->currentToken === false) {
            return;
        }

        if ($this->currentToken['type'] === 'identifier') {
            $this->consume('identifier');
            if ($this->currentToken !== false && $this->currentToken['type'] === 'operator') {
                if ($this->currentToken['value'] === '++' || $this->currentToken['value'] === '--') {
                    // Handle increment/decrement operators
                    $this->consume('operator');
                    $this->consume('semicolon');
                } else {
                    // Handle other operators
                    $this->consume('operator');
                    $this->parseExpression();
                    $this->consume('semicolon');
                }
                return;
            }
        } elseif ($this->currentToken['type'] === 'keyword') {
            $this->consume('keyword');
            $this->consume('identifier');
            if ($this->currentToken !== false && $this->currentToken['type'] === 'operator' && $this->currentToken['value'] === '=') {
                $this->consume('operator');
                $this->parseExpression();
                $this->consume('semicolon');
            }
            return;
        } elseif ($this->currentToken['type'] === 'scanf') {
            $this->consume('scanf');
            $this->consume('open_paren');
            $this->consume('string_literal');
            $this->consume('comma');
            $this->consume('operator');
            $this->consume('identifier');
            $this->consume('close_paren');
            $this->consume('semicolon');
            return;
        } else {
            // Consume the unrecognized token and throw an exception
            $token = $this->currentToken['value'];
            $this->consume($this->currentToken['type']);
            throw new Exception("Syntax error: Unexpected token {$token}");
        }
    }
}
?>
