const express = require('express');
const bodyParser = require('body-parser');
const app = express();
const io = require('socket.io').listen(3000)
const cors = require('cors');
const config = require('./config')


const whitelist = [] //white list for cron.

let corsOptions = {
  origin: function (origin, callback) {
    if (whitelist.indexOf(origin) !== -1) {
      callback(null, true)
    } else {
      callback(new Error('Not allowed by CORS'))
    }
  }
};

app.use(cors(config.whiteList));
app.use(express.urlencoded({extended: false}));
app.use(bodyParser.urlencoded({extended: true}));
app.use(bodyParser.json());


app.post('/', function (req, res) {
  io.sockets.emit('jackpot', req.body.jackpot)
  res.send();
});

app.listen(8080);