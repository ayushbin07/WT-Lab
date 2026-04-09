const input = document.getElementById("num1");
const clear = document.getElementById("AC");
const one = document.getElementById("1");
const two = document.getElementById("2");
const three = document.getElementById("3");
const four = document.getElementById("4");
const five = document.getElementById("5");
const six = document.getElementById("6");
const seven = document.getElementById("7");
const eight = document.getElementById("8");
const nine = document.getElementById("9");
const zero = document.getElementById("0");
const add = document.getElementById("add");
const subtract = document.getElementById("-");
const multiply = document.getElementById("multiply");
const divide = document.getElementById("divide");
const equals = document.getElementById("equals");
const decimal = document.getElementById(".");

function appendToDisplay(value) {
    input.value += value;
}

function clearDisplay() {
    input.value = "";
}


one.onclick = function() {
    appendToDisplay("1");
};
clear.onclick = clearDisplay;

two.onclick = function() {
    appendToDisplay("2");
};

three.onclick = function() {
    appendToDisplay("3");
};

four.onclick = function() {
    appendToDisplay("4");
};

five.onclick = function() {
    appendToDisplay("5");
};

six.onclick = function() {
    appendToDisplay("6");
};

seven.onclick = function() {
    appendToDisplay("7");
};

eight.onclick = function() {
    appendToDisplay("8");
};

nine.onclick = function() {
    appendToDisplay("9");
};  

zero.onclick = function() {
    appendToDisplay("0");
};

add.onclick = function() {
    appendToDisplay("+");
};

subtract.onclick = function() {
    appendToDisplay("-");
};

multiply.onclick = function() {
    appendToDisplay("*");
};

divide.onclick = function() {
    appendToDisplay("/");
};

decimal.onclick = function() {
    appendToDisplay(".");
};

divide.onclick = function() {
    appendToDisplay("/");
};

equals.onclick = function() {
    try {
        input.value = eval(input.value);
    } catch (error) {
        input.value = "Error";
    }
};