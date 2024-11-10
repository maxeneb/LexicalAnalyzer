import re

class Lexer:
    def __init__(self, input_string):
        self.input_string = input_string
        self.tokens = []
        self.current_position = 0

    def tokenize(self):
        while self.current_position < len(self.input_string):
            if self.input_string[self.current_position] == ' ':
                self.current_position += 1
                continue
            if self.is_number():
                self.tokens.append(('NUMBER', int(self.get_number())))
            elif self.is_identifier():
                self.tokens.append(('IDENTIFIER', self.get_identifier()))
            elif self.is_keyword():
                self.tokens.append(('KEYWORD', self.get_keyword()))
            else:
                raise ValueError("Unknown character")
            self.current_position += 1

    def is_number(self):
        return bool(re.match(r'^\d+', self.input_string[self.current_position:]))

    def get_number(self):
        start = self.current_position
        while self.current_position < len(self.input_string) and self.input_string[self.current_position].isdigit():
            self.current_position += 1
        return self.input_string[start:self.current_position]

    def is_identifier(self):
        return bool(re.match(r'^[a-zA-Z_][a-zA-Z0-9_]*$', self.input_string[self.current_position:]))

    def get_identifier(self):
        start = self.current_position
        while self.current_position < len(self.input_string) and self.input_string[self.current_position].isalnum():
            self.current_position += 1
        return self.input_string[start:self.current_position]

    def is_keyword(self):
        keywords = ['int', 'scanf', '+', '=', ';']
        for keyword in keywords:
            if self.input_string[self.current_position:].startswith(keyword):
                return True
        return False

    def get_keyword(self):
        start = self.current_position
        for keyword in ['int', 'scanf', '+', '=', ';']:
            if self.input_string[start:].startswith(keyword):
                self.current_position += len(keyword)
                return keyword
        raise ValueError("Unknown keyword")

    def get_next_token(self):
        if not self.tokens:
            self.tokenize()
        return self.tokens.pop(0)
class Parser:
    def __init__(self, lexer):
        self.lexer = lexer
        self.current_token = self.lexer.get_next_token()

    def parse(self):
        while self.current_token:
            if self.current_token[0] == 'KEYWORD':
                if self.current_token[1] == 'int':
                    self.parse_variable_declaration()
                elif self.current_token[1] == 'scanf':
                    self.parse_assignment()
                elif self.current_token[1] == '+':
                    self.parse_arithmetic_operation()
            self.current_token = self.lexer.get_next_token()

    def parse_variable_declaration(self):
        # Simplified for demonstration; actual implementation would require more checks
        print(f"Variable declaration: {self.current_token}")

    def parse_assignment(self):
        # Simplified for demonstration; actual implementation would require more checks
        print(f"Assignment: {self.current_token}")

    def parse_arithmetic_operation(self):
        # Simplified for demonstration; actual implementation would require more checks
        print(f"Arithmetic operation: {self.current_token}")
if __name__ == "__main__":
    input_string = """
    int a = 1;
    scanf("%d", %a);
    total += 1;
    counter++;
    """
    lexer = Lexer(input_string)
    parser = Parser(lexer)
    parser.parse()
