const http = require('http');

http.get('http://localhost:8000/', (res) => {
    let data = '';
    res.on('data', chunk => data += chunk);
    res.on('end', () => {
        const regex = /class="([^"]*card[^"]*)"/g;
        let m;
        const set = new Set();
        while ((m = regex.exec(data)) !== null) {
            set.add(m[1]);
        }
        console.log('Card classes on homepage:');
        console.log([...set]);
    });
});
