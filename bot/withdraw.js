if (row[0].wallet >= parseFloat(betAmount) || row[0].wallet_bonus > 0) {
    let texto = '`wallet` = `wallet`';
    if(row[0].wallet <= 0) texto = '`wallet_bonus` = `wallet_bonus`';
    let bonus = parseFloat(betAmount / 30);
    bonus = bonus.toFixed(2);
            connection.query('UPDATE `users` SET ' + texto + ' - '+parseFloat(betAmount)+', `total_bet` = `total_bet` + '+parseFloat(betAmount)+' WHERE `email` = '+connection.escape(user.email), function(err2, row2) {
            
                if(err2) {
                    return [socket.emit('notify','error','dicePlayFailed'),console.log(err2)];
                } else {
        if(row[0].bonus > 0) connection.query('UPDATE `users` SET `bonus` = `bonus` - ' + parseFloat(bonus) + ' WHERE `email` = '+connection.escape(user.email));
        connection.query('UPDATE `users` SET `anti_bot` = `anti_bot` + ' + parseFloat(betAmount) + ' WHERE `email` = '+connection.escape(user.email));
        connection.query('UPDATE `users` SET `wager` = `wager` + '+parseFloat(betAmount)+' WHERE `email` = '+connection.escape(user.email), function(err3) {
          if (err3) {
            console.log('error updating wager: ' + user.username);
            console.log(err3);
          }
          else{
            connection.query('INSERT INTO `wallet_change` SET `user` = '+connection.escape(user.email)+', `change` = -'+connection.escape(betAmount)+', `reason` = \'Dice '+'play'+'\'', function(err4, row3) {
              if(err4) {
                return [logger.error('important error at wallet_change'),console.log(err3),socket.emit('notify','error','serverError')];
              } else {
                if (users[user.email]) {
                  users[user.email].socket.forEach(function(asocket) {
                    if (io.sockets.connected[asocket])
                      io.sockets.connected[asocket].emit('balance change', parseInt('-' + betAmount));
                  });
                }
                var currgame = dice_games[user_dice_current[user.email]];
                var multiplier = ((100 / (((play.type == 0 || play.type == "0") ? play.limit : 10000 - play.limit)  / 100)) * (1 - 0.04));
                if (play.type == 0 || play.type == "0") {
                  if (currgame.roll < play.limit) {
                    //won
                    profit = betAmount * multiplier - betAmount;
                  } else {
                    profit = -betAmount;

                  }

                } else {
                  if (currgame.roll > play.limit) {
                    //won
                    profit = betAmount * multiplier - betAmount;
                  } else {
                    profit = -betAmount;
                  }
                }
                dice_games[user_dice_current[user.email]] = {
                  "hash": currgame.hash,
                  "id": dice_games.count,
                  "limit": play.limit,
                  "multiplier": multiplier,
                  "profit": profit,
                  "roll": currgame.roll,
                  "secret": currgame.secret,
                  "type": play.type,
                  "value": betAmount,
                  "user": {
                    "avatar": getLevelByLevel(user.level),
                    "id": user.username,
                    "rank": user.rank,
                    "username": user.username,
                    "email": user.email
                  }
                };
                if(profit > 0){
                  connection.query('UPDATE `users` SET ' + texto + ' + '+parseFloat(betAmount * multiplier)+', `total_bet` = `total_bet` + '+parseFloat(betAmount * multiplier)+' WHERE `email` = '+connection.escape(user.email), function(err5, row2) {
    