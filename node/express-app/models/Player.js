class Player {
  constructor(username) {
    this.username = username;
  }

  static current(req) {
    if (!req.session.username) {
      req.session.username = 'Guest';
    }
    return new Player(req.session.username);
  }
}

module.exports = Player;
