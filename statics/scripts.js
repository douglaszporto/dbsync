document.addEventListener("DOMContentLoaded", () => {

    const btnCompare = document.querySelector('#btn-compare');
    const request = new XMLHttpRequest();

    request.onreadystatechange = function() {
        if(request.readyState === 4) {
            if(request.status === 200) { 
                buildDiff(JSON.parse(request.responseText));
            } 
        }
    }

    
    btnCompare.addEventListener('click', function() {
        const db1 = document.querySelector('#input-database-1').value;
        const db2 = document.querySelector('#input-database-2').value;
        request.open('Get', '/diff?db1=' + db1 + '&db2=' + db2);
        request.send();
    });


    const buildDiff = (diffs) => {
        let template = document.querySelector('#template-query').content;

        document.querySelector('#database-diff').innerHTML = "";

        const types = ["table", "field"];
        let count = 0;
        for(let type in types) {
            console.log(type);
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
                            element.parentNode.removeChild(element);
                        });
                    });

                    clone.querySelector('.query-exec-element-execute').addEventListener('click', (e) => {
                        e.stopPropagation();
                        e.preventDefault();

                        elements = document.querySelectorAll('.id-' + diffs[types[type]][i]["id"]);
                        elements.forEach(element => {
                            element.parentNode.removeChild(element);
                        });
                    });

                    document.querySelector('#database-diff').appendChild(clone);
                }
            }
        }
    };

});