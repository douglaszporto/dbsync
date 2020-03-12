document.addEventListener("DOMContentLoaded", () => {

    const btnCompare = document.querySelector('#btn-compare');
    const requestDiff = new XMLHttpRequest();
    const requestExec = new XMLHttpRequest();

    requestDiff.onreadystatechange = function() {
        if(requestDiff.readyState === 4) {
            if(requestDiff.status === 200) { 
                buildDiff(JSON.parse(requestDiff.responseText));
            } 
        }
    }

    document.querySelectorAll('#input-database-1, #input-database-2').forEach((item) => {
        item.addEventListener('change', (e) => {
            const db1 = document.querySelector('#input-database-1').value;
            const db2 = document.querySelector('#input-database-2').value;

            if (db1.length > 0 && db2.length > 0 && db1 !== db2) {
                btnCompare.classList.remove('disabled');
            } else {
                btnCompare.classList.add('disabled');
            }
        })
    });
    
    btnCompare.addEventListener('click', function() {
        const db1 = document.querySelector('#input-database-1').value;
        const db2 = document.querySelector('#input-database-2').value;
        requestDiff.open('Get', '/diff?db1=' + db1 + '&db2=' + db2);
        requestDiff.send();

        btnCompare.innerHTML = "";
        btnCompare.classList.add('loading');
    });


    const buildDiff = (diffs) => {

        btnCompare.innerHTML = "Comparar";
        btnCompare.classList.remove('loading');

        let template = document.querySelector('#template-query').content;

        document.querySelector('#database-diff').innerHTML = "";

        if (diffs["table"].length === 0 && diffs["field"].length === 0) {
            let message = document.createElement('pre');
            message.classList.add('query-message');
            message.innerHTML = "Não há diferenças entre os servidores selecionados";
            document.querySelector('#database-diff').appendChild(message);
        }

        const types = ["table", "field"];
        let count = 0;
        for(let type in types) {
            for(let i in diffs[types[type]]) {
                count++;
                let operations = ['sql','reversesql'];

                for(let j in operations) {
                    let clone = template.querySelector('.query-exec-wrapper').cloneNode(true);
                    
                    clone.classList.add('from'+diffs[types[type]][i][operations[j] == 'sql' ? 'from' : 'to']);
                    clone.classList.add(operations[j]);
                    clone.classList.add(types[type]);
                    clone.classList.add('query-exec-background-' + (count % 2 == 0 ? 'even' : 'odd'));
                    clone.classList.add('id-' + diffs[types[type]][i]["id"]);

                    clone.querySelector('.query-exec-element-query').innerHTML = diffs[types[type]][i][operations[j]];

                    clone.querySelector('.query-exec-element-options').addEventListener('click', (e) => {
                        e.stopPropagation();
                        e.preventDefault();

                        e.target.classList.toggle('active');
                    });

                    clone.querySelector('.query-exec-element-ignore').addEventListener('click', (e) => {
                        e.stopPropagation();
                        e.preventDefault();

                        elements = document.querySelectorAll('.id-' + diffs[types[type]][i]["id"]);
                        
                        elements.forEach(element => {
                            element.style.height = element.childNodes[1].offsetHeight + 'px';
                            setTimeout(() => {
                                element.classList.add('removed');
                            },1);
                        });
                    });

                    clone.querySelector('.query-exec-element-execute').addEventListener('click', (e) => {
                        e.stopPropagation();
                        e.preventDefault();

                        let sql = diffs[types[type]][i][operations[j]];
                        let execOn = document.querySelector('#input-database-2').value;

                        if ((diffs[types[type]][i].from == '1' && operations[j] === 'reversesql') || 
                            (diffs[types[type]][i].from == '2' && operations[j] === 'sql')) {
                                execOn = document.querySelector('#input-database-1').value;
                        }

                        requestExec.onreadystatechange = function() {
                            if(requestExec.readyState === 4) {
                                if(requestExec.status === 200) { 
                                    const res = JSON.parse(requestExec.responseText);
                                    
                                    const classSql = operations[j] === 'sql' ? '.reversesql' : '.sql';
                                    const elementRemove = document.querySelector('.id-' + diffs[types[type]][i]["id"] + classSql);
                                    const elementExec = document.querySelector('.id-' + diffs[types[type]][i]["id"] + '.' +  operations[j]);
                                    
                                    elementRemove.style.height = elementRemove.childNodes[1].offsetHeight + 'px';
                                    setTimeout(() => {
                                        elementRemove.classList.add('removed');
                                    },1);
                                    
                                    elementExec.classList.add('executed');
                                    
                                    let message = document.createElement('pre');
                                    message.innerHTML = res.message;
                                    if (res.status === 'fail') {
                                        message.classList.add('query-error');
                                    } else if (res.status === 'ok') {
                                        message.classList.add('query-success');
                                    }
                                    elementExec.childNodes[1].appendChild(message);

                                } 
                            }
                        }

                        requestExec.open('Post', '/exec?db=' + execOn, true);
                        requestExec.send(`{
                            "sql" : "${sql}"
                        }`);

                        /*elements = document.querySelectorAll('.id-' + diffs[types[type]][i]["id"]);
                        elements.forEach(element => {
                            element.parentNode.removeChild(element);
                        });*/
                    });

                    document.querySelector('#database-diff').appendChild(clone);
                }
            }
        }
    };

});