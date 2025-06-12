const crypto = require('crypto');
const Player = require('../models/Player');

class HomeController {
  static index(req, res) {
    const user = Player.current(req);
    const args = {
      USERNAME: user.username,
    };
    if (!req.session.networkKey) {
      req.session.networkKey = crypto.randomBytes(8).toString('hex');
    }
    args.NETWORKKEY = req.session.networkKey;
    res.render('home.twig', args, (err, body) => {
      if (err) {
        res.status(500).send(err.message);
        return;
      }
      args.BODY = body;
      res.render('layout.twig', args);
    });
  }
}

module.exports = HomeController;
