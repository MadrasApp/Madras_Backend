let ValiditionCode
const TTL = 60

$('.form-input').on('focus', (e) => {
    e.target.parentNode.classList.remove('invalid')
})



const startTimer = () => {
    let timerContainer = $('.timer') 
    let timer = document.getElementById('timeLeft');
    timerContainer.css({
        'display': 'flex',
        'color':'black'
    })
    let g = TTL
    timer.innerHTML = g
    const x = setInterval(function () {
        g--
        timer.innerHTML = g
        if(g <= 20 ){
            timerContainer.css('color','red') 
        }

        if (g == 0) {
            ValiditionCode = null
            timer.innerHTML=0
            clearInterval(x);
            $('#codeInput').css('display', 'flex')
            $('#codeInput').fadeOut("slow", function () {
            });
            
            $('#codeSubmit').fadeOut("fast", function () {
                $('#phoneSubmit').fadeIn("slow", function () {

                });
            });
            timerContainer.css('display' , 'none')


        }

    }, 1000)
}


const submitPhone = async () => {
    let phoneNumber = document.getElementById('phone-number').value;

    if (!phoneNumber) {
        document.getElementById('phone-number').parentNode.classList.add('invalid')
        $('#m-1').html('&#1601;&#1740;&#1604;&#1583; &#1576;&#1575;&#1740;&#1583; &#1662;&#1585; &#1588;&#1608;&#1583;')
    }
    else {
        ValiditionCode = Math.floor(100000 + Math.random() * 900000)

        startTimer()

        $('#codeInput').css('display', 'flex')
        $('#codeInput').fadeIn("slow", function () {
        });
        $('#phoneSubmit').fadeOut("fast", function () {
            $('#codeSubmit').fadeIn("slow", function () {
            });
        });


        try {


            const xhr = new XMLHttpRequest();
            const url = 'http://droose-howzeh2.ir/api/v2/SendUserSMS';
            const params = `token=C36ZKdE02Nf89MIylUpbgL5VDnjArHmX&MessageType=1&mac=${phoneNumber}&Message=${ValiditionCode}`;
            xhr.open('POST', url, true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded','Access-Control-Allow-Origin', '*');

            xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                console.log(xhr.responseText);
            }
            }
            xhr.send(params);


        
        } catch (error) {
            console.log(error)
        }


        console.log(phoneNumber, ValiditionCode)
    }

}


const submitCode = () => {
    let phoneNumber = document.getElementById('phone-number').value;
    let codeNumber = document.getElementById('code-number').value;


    if (!phoneNumber || !codeNumber) {
        if (!phoneNumber) {
            document.getElementById('phone-number').parentNode.classList.add('invalid')
            $('#m-1').html('&#1601;&#1740;&#1604;&#1583; &#1576;&#1575;&#1740;&#1583; &#1662;&#1585; &#1588;&#1608;&#1583;')
        } else {
            document.getElementById('code-number').parentNode.classList.add('invalid')
            $('#m-2').html('&#1601;&#1740;&#1604;&#1583; &#1576;&#1575;&#1740;&#1583; &#1662;&#1585; &#1588;&#1608;&#1583;')
        }

    }
    else {
        if (codeNumber == ValiditionCode) {
            console.log("deleted")

            const xhr = new XMLHttpRequest();
            const url = `http://droose-howzeh2.ir/api/v2/DeleteUser/${phoneNumber}`;
            xhr.open('POST', url);

            xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                console.log(xhr.responseText);
            }
            }
            xhr.send();


            $('#phoneForm').fadeOut("slow", function () {
                $('.confrimation').fadeIn("fast", function () {
                });
            });

        }
        else {
            console.log("not deleted")
        }

    }


}

