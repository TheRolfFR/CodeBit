document.addEventListener('DOMContentLoaded', function(){
    if(document.getElementById('login')) {
        document.getElementById('login').addEventListener('click', function() {
            let y = vex.dialog.buttons.YES;
            y.text = 'Login';
            let n = vex.dialog.buttons.NO;
            n.text = 'Back';
            vex.dialog.open({
                message: 'Enter your password:',
                input: [
                    '<input name="password" type="password" placeholder="Password" required />'
                ].join(''),
                buttons: [
                    y, n
                ],
                callback: function (data) {
                    if (data) {
                        console.log(data);
                        window.location.search = '?pass=' + data.password
                    }
                }
            })
        })
    }
})
