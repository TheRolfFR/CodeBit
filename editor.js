const classes = ['', 'vertical', 'jsfiddle'];

document.addEventListener('DOMContentLoaded', function(){
    
    CodeBit.init();
    
    document.getElementById('overlay').addEventListener('click', function() {
        const editors = document.getElementById('editors')
        const currentclass = editors.attributes.class.value.split(' ')[1] || ''
        
        if(currentclass !== '') {
            editors.classList.remove(currentclass)
            editors.classList.add(classes[(classes.indexOf(currentclass)+1)%classes.length])
        } else {
            editors.classList.add(classes[1])
        }
    })
    
    document.getElementById('save').addEventListener('click', function() {
        CodeBit.saveBit()
    })
    
    document.getElementById('title').addEventListener('keypress', function(event){
        console.log(event)
        if (event.which == 13 || event.keyCode == 13) {
            CodeBit.saveBit()
        }
        document.title = event.target.value
    })
})

const CodeBit = {
    htmleditor: undefined,
    csseditor: undefined,
    jseditor: undefined,
    timeout: undefined,
    preview: undefined,
    delay: 3000,
    
    init: function() {
        var that = this;
        this.timeout = setTimeout(() => {}, 0)
        this.preview = document.getElementById('preview')
        
        this.htmleditor = ace.edit("html", {
            showPrintMargin: false,
            enableBasicAutocompletion: true
        });
        this.htmleditor.setOption("enableEmmet", true)
        this.htmleditor.setTheme("ace/theme/monokai")
        this.htmleditor.session.setMode("ace/mode/html")
        this.htmleditor.getSession().setUseWorker(false)
        this.htmleditor.session.setUseWrapMode(true)
        this.htmleditor.session.on('change', () => {
            clearTimeout(that.timeout)
            that.timeout = setTimeout(() => { that.updateContent() }, this.delay)
        })
        this.htmleditor.commands.addCommand({
            name: 'Save',
            bindKey: {win: 'Ctrl-S',  mac: 'Command-S'},
            exec: function(editor) {
                that.saveBit()
            }
        })
        
        
        
        this.csseditor = ace.edit("css", {
            showPrintMargin: false,
            enableBasicAutocompletion: true
        })
        this.csseditor.setOption("enableEmmet", true)
        this.csseditor.setTheme("ace/theme/monokai")
        this.csseditor.session.setMode("ace/mode/css")
        this.csseditor.getSession().setUseWorker(false)
        this.csseditor.session.setUseWrapMode(true)
        this.csseditor.session.on('change', () => {
            clearTimeout(that.timeout)
            that.timeout = setTimeout(() => { that.updateContent() }, this.delay)
        })
        this.csseditor.commands.addCommand({
            name: 'Save',
            bindKey: {win: 'Ctrl-S',  mac: 'Command-S'},
            exec: function(editor) {
                that.saveBit()
            }
        })
        
        
        
        this.jseditor = ace.edit("js", {
            showPrintMargin: false,
            enableBasicAutocompletion: true
        })
        this.jseditor.setOption("enableEmmet", true)
        this.jseditor.setTheme("ace/theme/monokai")
        this.jseditor.session.setMode("ace/mode/javascript")
        this.jseditor.getSession().setUseWorker(false)
        this.jseditor.session.setUseWrapMode(true)
        this.jseditor.session.on('change', () => {
            clearTimeout(that.timeout)
            that.timeout = setTimeout(() => { that.updateContent() }, this.delay)
        })
        this.jseditor.commands.addCommand({
            name: 'Save',
            bindKey: {win: 'Ctrl-S',  mac: 'Command-S'},
            exec: function(editor) {
                that.saveBit()
            }
        })
        
        this.intializeContent()
        this.updateContent()
    },
    
    intializeContent: function() {
        var doc = this.preview.contentWindow.document
        doc.open()
        doc.write('<html>\
            <head>\
                <meta charset="utf-8">\
                <style></style>\
                <script></script>\
            </head>\
            <body></body>\
        </html>')
        doc.close()
    },
    
    updateContent: function() {
        if(this.preview.contentDocument.querySelector('body').innerHTML != this.htmleditor.getValue()) {
            this.preview.contentDocument.querySelector('body').innerHTML = this.htmleditor.getValue()
        }
        if(this.preview.contentDocument.querySelector('head style').innerHTML != this.csseditor.getValue()) {
            this.preview.contentDocument.querySelector('head style').innerHTML = this.csseditor.getValue()
        }
        if(this.preview.contentDocument.querySelector('head script').innerHTML != this.jseditor.getValue()) {
            this.preview.contentDocument.querySelector('head script').innerHTML = this.jseditor.getValue()
        }
    },
    
    saveBit: function() {
        let json = {
            'html' : this.htmleditor.getValue(),
            'css' : this.csseditor.getValue(),
            'js' : this.jseditor.getValue(),
            'id' : window.location.search.replace('?', '').split('=')[1] || '',
            'json' : JSON.stringify({
                'title': document.getElementById('title').value,
                'editorconfig': document.getElementById('editors').attributes.class.value.split(' ')[1] || ''
            })
        }
        
        postRequest('save.php', json, function(res, err){
            if(err) {
                console.error(res);
                Push.create('Error : ' + res.response, {
                    body: 'Check console for more details.'
                })
            } else {
                if(res != "done") {
                    window.location.search = "?id=" + res;
                } else {
                    if(json.json['title'] != 'Untitled') {
                        document.title = document.getElementById('title').value
                    }
                    Push.create('Bit saved!', {
                        timeout: 1500,
                        onClick: function () {
                            window.focus();
                            this.close();
                        }
                    })
                }
            }
        });
    }
}