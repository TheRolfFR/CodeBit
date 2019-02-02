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
    
    document.getElementById('settings').addEventListener('click', function(){
        vex.dialog.open({
    message: 'Codebit options',
    input: [
        '<label for="stuffhead">Stuff for &lt;head&gt;</label>',
        '<div>',
            '<textarea id="stuffhead" rows="5" name="stuffhead" placeholder="e.g. <meta>, <link>, <script>">' + CodeBit.stuffhead + '</textarea>',
        '</div>',
        '<label for="externalcss">External JS</label>',
        '<p class="note">Seperate with return character</p>',
        '<div>',
            '<textarea id="externaljs" rows="5" name="externaljs" placeholder="http://link.to/your.js">' + CodeBit.externaljs + '</textarea>',
        '</div>',
        '<label for="stuffhead">External CSS</label>',
        '<p class="note">Seperate with return character</p>',
        '<div>',
            '<textarea id="externalcss" rows="5" name="externalcss" placeholder="http://link.to/your.css">' + CodeBit.externalcss + '</textarea>',
        '</div>',
    ].join(''),
    callback: function (data) {
        if (data) {
            CodeBit.stuffhead = data.stuffhead || "";
            CodeBit.externalcss = data.externalcss || "";
            CodeBit.externaljs = data.externaljs || "";
            
            CodeBit.saveBit();
        }
        
    }
});
    });
});

String.prototype.escapeHTML = function() {
  var map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };

  return this.replace(/[&<>"']/g, function(m) { return map[m]; });
}
String.prototype.encodeHTML = function() {
  var map = {
    '&amp;' : '&',
    '&lt;'  : '<',
    '&gt;'  : '>',
    '&quot;': '"',
    '&#039;': "'"
  };

  return this.replace(/[&<>"']/g, function(m) { return map[m]; });
}

const CodeBit = {
    htmleditor: undefined,
    csseditor: undefined,
    jseditor: undefined,
    
    stuffhead: undefined,
    externalcss: undefined,
    externaljs: undefined,
    
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
        
        this.stuffhead = document.getElementById('stuffheadhidden').innerHTML || ""
        this.externalcss = document.getElementById('externalcsshidden').innerHTML || ""
        this.externaljs = document.getElementById('externaljshidden').innerHTML || ""
        
        this.updateContent()
    },
    
    updateContent: function() {
        var doc = this.preview.contentWindow.document
        doc.open()
        
        // prepare js
        let scriptsrender = "";
        if(this.externaljs != '') {
            const scripts = this.externaljs.split('\n');
            for(let k in scripts) {
                scriptsrender += '<script src="' + scripts[k] + '"></script>';
            }
        }
        
        // prepare css
        let stylesrender = "";
        if(this.externalcss != '') {
            const styles = this.externalcss.split('\n');
            for(let k in styles) {
                stylesrender += '<link rel="stylesheet" href="' + styles[k] + '">';
            }
        }
        
        doc.write('<html>\
            <head>\
                <meta charset="utf-8">\
                ' + scriptsrender + '\
                ' + stylesrender + '\
                <script>' + this.jseditor.getValue() + '</script>\
                <style>' + this.csseditor.getValue() + '</style>\
            </head>\
            <body>\
                ' + this.htmleditor.getValue() + '\
            </body>\
        </html>')
        doc.close()
    },
    
    saveBit: function() {
        let json = {
            'html' : this.htmleditor.getValue(),
            'css' : this.csseditor.getValue(),
            'js' : this.jseditor.getValue(),
            'id' : window.location.search.replace('?', '').split('=')[1] || '',
            'json' : JSON.stringify({
                'title': document.getElementById('title').value,
                'editorconfig': document.getElementById('editors').attributes.class.value.split(' ')[1] || '',
                'stuffhead': this.stuffhead,
                'externalcss': this.externalcss,
                'externaljs': this.externaljs
            }),
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