// See: https://gist.github.com/eriksencosta/3bb7e774bf3f585f4642fcde2b798402
// See: https://docs.google.com/spreadsheets/d/1zUnoos06GrEBCmNY0RQCzpim_dyx5OEyAcy-9pnF0do/edit#gid=0
// See: https://intercom.help/godigibee/en/articles/2950116-script
var body = {
  "number" : "26145792"
};
const DIGITS_LENGTH = 8;
const MULTIPLIER_SEQUENCE = "26";

let generateMultiplier = function(numberOfDigits) {
    if (numberOfDigits <= MULTIPLIER_SEQUENCE.length) {
        throw `numberOfDigits must be greater than the multiplier sequence length (${MULTIPLIER_SEQUENCE.length}).`;
    }

    let multiplierRepetition = Math.ceil(numberOfDigits / MULTIPLIER_SEQUENCE.length);

    return (MULTIPLIER_SEQUENCE.repeat(multiplierRepetition)).substr(0, numberOfDigits);
};

let calculateVerificationDigit = function(number) {
    if (number.length != DIGITS_LENGTH) {
        throw `The number must have exactly ${DIGITS_LENGTH} digits.`;
    }

    let convertToInt = function(value) {
        return parseInt(value, 10);
    };

    let multipliers = generateMultiplier(DIGITS_LENGTH);

    let digitsSum = number.split('').reduce((acc, value, index) => {
        let digit = convertToInt(value);
        let multiplier = convertToInt(multipliers[index]);
        let multiplication = digit * multiplier;

        return acc + Math.trunc(multiplication / 10 + multiplication % 10);
    }, 0);

    return 9 - (digitsSum % 10);
};

let appendVerificationDigit = function(number) {
    return number + calculateVerificationDigit(number);
};

try {
    output = { numberWithVerificationDigit: appendVerificationDigit(body.number) };
} catch(e) {
    output = { error: "Error while generating the verification digit: " + e };
}

console.log(output);