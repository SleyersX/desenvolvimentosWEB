const months = [];
months.push('Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez');

let dtStart = null;
let dtEnd = null;
let execution = null;

function getDtStartAndDtEnd(timestamp, dayExec, dayExcep) {
    const now = new Date(timestamp);
    const yearCurrent = now.getFullYear();
    const monthNameCurrent = months[now.getMonth()];
    const monthNamePrevious = monthNameCurrent === 'Jan' ? months[11] : months[now.getMonth() -1];
    const yearPrevious = monthNameCurrent === 'Jan' ? now.getFullYear()-1 : now.getFullYear();
    const monthCurrent = String("00"+(now.getMonth()+1)).slice(-2);
    const monthPrevious = monthNameCurrent === 'Jan' ? 12 : String("00"+((now.getMonth()-1)+1)).slice(-2);
    const dayCurrent = now.getDate().toString();
    const dayExecution = dayExec.toString();
    const dayException = dayExcep;

    if(monthNameCurrent === 'Fev' && dayCurrent === dayException){
        dtStart = `${yearCurrent}${monthPrevious}${String("00"+dayExecution.toString()).slice(-2)}`;
        dtEnd = `${yearCurrent}${monthCurrent}${dayException}`;
        execution = true;
    }else if(monthNamePrevious === 'Fev' && dayExecution === dayCurrent && dayCurrent < (dayException+1)){
        dtStart = `${yearCurrent}${monthPrevious}${String("00"+(parseInt(dayExecution)-parseInt(dayCurrent))+parseInt(dayExecution)).slice(-2)}`;
        dtEnd = `${yearCurrent}${monthCurrent}${String("00"+(parseInt(dayExecution)-1).toString()).slice(-2)}`;
        execution = true;
    }else if(monthNamePrevious === 'Fev' && dayExecution === dayCurrent && dayCurrent >= (dayException+1)) {
        dtStart = `${yearPrevious}${monthPrevious}${String("00"+dayException.toString()).slice(-2)}`;
        dtEnd = `${yearCurrent}${monthCurrent}${String("00"+(parseInt(dayExecution)-1).toString()).slice(-2)}`;
        execution = true;
    }else if(dayExecution === dayCurrent){
        dtStart = `${yearPrevious}${monthPrevious}${String("00"+dayExecution.toString()).slice(-2)}`;
        dtEnd = `${yearCurrent}${monthCurrent}${String("00"+(parseInt(dayExecution)-1).toString()).slice(-2)}`;
        execution = true;
    }else{
        dtStart = null;
        dtEnd = null;
        execution = false;
    }
}

function main(timestamp){

    const now = new Date(timestamp);
    // Verificar ano bissexto
    const leapYear = now.getFullYear() % 4;

    if(leapYear === 1){
        getDtStartAndDtEnd(timestamp, body.dayExecution,28);
    }else{
        getDtStartAndDtEnd(now, body.dayExecution,29);
    }
}

const body = {
    "dayExecution" : "5",
    "now" : 1614942000000
};

if(typeof body !== "undefined"){
    if (body.dayExecution !== undefined && body.now !== undefined) {
        main(body.now);
    }
}

output = {
    execution: execution,
    dtStart: dtStart,
    dtEnd: dtEnd
};

console.log(output);