const http = require('http');

const hostname = '127.0.0.1';
const port = 3000;

const server = http.createServer((req, res) => {
    res.statusCode = 200;
    res.setHeader('Content-Type', 'text/html; charset=utf-8');
    res.end('<h1>Вітаю! Це мій веб-сервер на Node.js ЛР13 виконав Зозуля В.В. 🚀</h1>');
});

server.listen(port, hostname, () => {
    console.log(`Сервер успішно запущено! Відкрийте в браузері: http://${hostname}:${port}/`);
});