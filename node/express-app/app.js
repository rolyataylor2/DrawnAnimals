const express = require('express');
const session = require('express-session');
const nunjucks = require('nunjucks');
const path = require('path');

const HomeController = require('./controllers/HomeController');

const app = express();
const PORT = process.env.PORT || 3000;

const templatePath = path.join(__dirname, 'templates');
nunjucks.configure(templatePath, {
  autoescape: true,
  express: app,
});

app.use(express.static(path.join(__dirname, '..', '..', 'html')));

app.use(
  session({
    secret: 'drawnanimals-secret',
    resave: false,
    saveUninitialized: true,
  })
);

app.get('/', (req, res) => HomeController.index(req, res));

app.listen(PORT, () => {
  console.log(`Express server running on port ${PORT}`);
});
