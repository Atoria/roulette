const express = require('express');
const bodyParser = require('body-parser');
const app = express();
const io = require('socket.io').listen(3000)


app.use(express.urlencoded({extended: false}));
app.use(bodyParser.urlencoded({extended: true}));
app.use(bodyParser.json());


app.post('/', function (req, res) {
  io.sockets.emit('jackpot', req.body.jackpot)
  res.send();
});

app.listen(8080);