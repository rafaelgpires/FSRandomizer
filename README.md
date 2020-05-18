# FSRandomizer
FSRandomizer allows you to create your own career in Clone Hero with the entire Guitar Hero series scaled by difficulty (with some surprises!) so that revisitting the games is as fun and exciting as playing career for the first time!

# Notes
Removed Ted Nugent and Zakk Wylde guitar battle from the FS to make an even 660 songs,\
Due to Clone Hero having no "battle" in Quickplay or career mode to simulate those anyway.

# Description
This website randomizes the full series list with a set of configurable options.\
Once the list has been created, it functions as a fully featured "FC Tracker", with an optional [app](https://github.com/rafaelgpires/FSRandomizer-App) to download.

### Features: Generation
  - **Generation**: Randomize the full series on custom settings based on breakdown.txt.
  - **Options**: Click the "menu" item underneath generate to access options,
      - **Songs per Chapter**: Self-descriptive, but also determines amount of encores (1 for each 5 songs),
      - **Difficulty Variance**: The original list is sorted by difficulty, this picks up songs from the bottom X% of the list,
      - **Difficult Encore (% Chance)**: The chance an encore can have a difficulty bonus (recommended: 100%),
      - **Difficult Encore (% Bonus)**: The amount of difficulty added to the normal variance, with variance difficulty now being minimum,
      - **Super Encore (% Chance)**: The chance an encore can turn "Super" with an increased difficulty bonus,
      - **Super Encore (% Bonus)**: Identical to the previous difficulty increment,
      - **Reset Encores**: Whether the list will reset the proc variables on each encore instead of each chapter - i.e., if you don't reset and a "Difficult Encore" procs, the remaining encores of that chapter will all be "Difficult Encore" or above, never below (recommended: off).
      
### Features: Online
  - **Share your List**: Check other people's lists by just typing their list ID in the URL, or using the navbar input.
  - **Authenticating Owner**: Generates a password on creation that never gets displayed again, this password allows you to edit the list, creating a login system that doesn't need you to register. You're automatically logged to whatever list you create.
  - **Responsive Javascript**: Page adapts in real-time to your changes without needing to reload to update the server.

### Features: List
  - **List name**: Simply click the title to edit it, limit 13 characters.
  - **List description**: Simply click the subtitle to edit it, limit 45 characters.
  - **FC Tracker**: Enable the FC tracker, this action will enable "Stats" and add a column that holds a crown representing whether a song has been FC'd, an FC also makes it so the speed value attached to that song counts towards the speed average.
      - **Stats**: Displays the totals associated with your list (amount of FCs, average Speed per FC, total score accumulated),
      - **Speed**: Enabled the speed stat and a column that allows you to give the maximum speed you've gotten the FC at,
      - **Score**: Enabled the score stat and a column that allows you to give the maximum score you've achieved in this song,
      - **Proofs**: Both Speed and Score allow you to submit proof of the claim with a URL (assumed an image or video).

### Features: Flavour
  - **List Header Icon**: Reaching high FC counts unlocks a big "Difficulty Icon" above your list that represents your achievement.
  - **Difficulty Icons**: Super encore procs at difficulty 5 or above are prefixed by a "Difficulty Icon".
  - **Difficulty Color Scale**: Difficulty of each song is color-coded from cold to hot for easier viewing.

# Future Features
  - **Leaderboards**: A leaderboards page and a proof-verification system for record-beating submissions.
  - **DLC**: Optional DLC implementation in the list if there's any interest.
  - **RB Series**: Sister website with the RB series if there's any interest.
  - **Custom FC Tracker**: Sister website with Chorus integration to allow for user-made lists tracking Chorus' customs.
  - **Custom Song Leaderboards**: Chorus integrated-website will have per-song leaderboards (highest scores! highest speeds!).
