<?php
/** Headline and Article Generator
 * Headlines and Articles are deposited in the arrays in decending order of importance.
 * These articles are ECHO'ed by TWIG.
 * The Main headline is determined by the day of the week.
 * The next headlines are randomly placed in based on whatever.
 */
$Headline = array();
$Article = array();
$Image = array();
switch(date('N')) {
    case 1:// Monday - New Pokemon Day
        $Headline[] = 'New Pokemon Discovered!';
        $Article[] = 'Encounter this new pokemon located ....';
        $Image[] = '<img src=""/>';
        // @todo Pull latest pokemon to be released from system_drawnimals, if this pokemon is powerful keep it a mystery.
        break;
    case 2:// Tuesday - Notable Pet Day, Lottery Ticket Buying
        $Headline[] = 'PLAYER Drawnimal Showcase: PETNAME';
        $Article[] = 'This Drawnimal was caught in LOCATION and is notible for its age';
        $Image[] = '<img src=""/>';
        // @todo Also pull random pokemon from user_drawnimals, based on greatest (level,hp,atk,def,spatk,spdef,likes,comments)
        //       Try not to pull the same pokemon twice...
        $Headline[] = 'Lottery starts a new drawing.';
        $Article[] = 'Be sure to visit veridian city to buy a ticket, this week there is a bonus item';
        $Image[] = '<img src=""/>';
        break;
    case 3:// Wednesday - Fan Art Submissions
        $Headline[] = 'Pokemon Fan Art Contest';
        $Article[] = 'This weeks subject is: Pikachu!';
        $Image[] = '<img src=""/>';
        $Headline[] = 'Fanfic title';
        $Article[] = 'Fanfic story';
        $Image[] = '';
        break;
    case 3:// Thursday - Event Notification day.
        $Headline[] = 'Extra Extra! Proffessor Elm discusses Pokemon.';
        $Article[] = 'This weeks subject is: Pikachu!';
        $Image[] = '<img src=""/>';
        break;
    case 3:// Friday - Fan Art Vote
        $Headline[] = 'Time To Vote!';
        $Article[] = 'The entries are in time to vote! lets do this.';
        $Image[] = '<img src=""/>';
        break;
    case 3:// Saturday - Announce Winner of fan art
        $Headline[] = 'And the winner is!';
        $Article[] = 'User:PLAYER';
        $Image[] = '<img src=""/>';
        break;
    case 3:// Sunday - Lottery Givaway
        $Headline[] = 'Winner of the lottery';
        $Article[] = 'The winner is :: and they won ::';
        $Image[] = '<img src=""/>';
        break;
}
// Event news
// Pokemon news
// Player news
// Item news
// Website news
// Holiday News
// Downtime News
// Preview News
// Review News
// New Color News
// Unusual News
// User Award News
// @todo LOTS OF WORK. Pull from databases social_timeline, social_likes
