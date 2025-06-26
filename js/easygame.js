// easygame.js

// Make sure you define this *before* loading easygame.js in your HTML:
// <script>const QUIZ_ID = 'QZ01';</script>

let rollingSound = new Audio('../audio/dice-rolling.mp3');
let winSound     = new Audio('../audio/win.mp3');

let p1sum = 0;
let num   = 0;
let score = 0;
let playerHasRolled = false; // Track if player has actually started playing

const jumps = {
  7:  28, 16: 24, 31: 71, 36: 19,
  46: 65, 60: 78, 64: 42,
  69: 29, 73: 54, 98: 66
};

const snakePaths = {
  98: [83, 77, 76, 75, 66],
  73: [68, 67, 54],
  64: [58, 42],
  69: [29],
  36: [19]
};

function getLevel(score) {
  if (score <= 20) return "Beginner";
  if (score <= 40) return "Apprentice";
  if (score <= 60) return "Coder";
  if (score <= 80) return "Debugger";
  if (score <= 99) return "Architect";
  return "Java Master";
}

function updateScoreDisplay() {
  const level = getLevel(score);
  document.getElementById("scoreDisplay").innerText =
    `Score: ${score} | Level: ${level}`;
}

function saveProgressToDatabase(reset = false) {
  // Don't save progress if player hasn't actually played yet
  // unless explicitly forced with reset=true
  if (!playerHasRolled && !reset) {
    return;
  }
  
  const level = getLevel(score);
  const urlParams = new URLSearchParams(window.location.search);
  const isNewGame = urlParams.get('new') === '1';
  const forceNew = urlParams.get('force_new') === '1';
  
  // Key to track if we've already created an entry for this specific game instance
  const gameInstanceKey = `game_instance_${QUIZ_ID}_${Date.now()}`;
  const entryCreatedKey = `game_${QUIZ_ID}_entry_created`;
  const entryCreated = sessionStorage.getItem(entryCreatedKey);
  
  let shouldReset = false;
  
  // Create a new entry if:
  // 1. This is a forced reset (from game completion)
  // 2. OR this is a new game AND we haven't created an entry for this specific game instance
  if (reset || isNewGame || forceNew) {
    // Only set shouldReset if we haven't created an entry for this specific game instance
    if (!entryCreated) {
      shouldReset = true;
      // Mark that we've created an entry for this specific game instance
      sessionStorage.setItem(entryCreatedKey, gameInstanceKey);
    }
  }
  
  const params = new URLSearchParams({
    quiz_id:    QUIZ_ID,
    score:      score,
    level:      level,
    lst_played: p1sum,
    reset:      shouldReset ? 1 : 0
  });

  fetch("../student/save-progress.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params.toString()
  })
    .then(r => r.json())
    .then(data => {
      if (!data.success) console.error("Save failed:", data.message);
      else console.log("Progress saved:", data.message);
    })
    .catch(err => console.error("Error saving progress:", err));
}

function handleWin() {
  winSound.play();
  
  // Ensure position 100 is recorded in the database
  p1sum = 100;
  updateScoreDisplay();
  
  // Save progress with the final position but don't create new entry
  const params = new URLSearchParams({
    quiz_id:    QUIZ_ID,
    score:      score,
    level:      getLevel(score),
    lst_played: p1sum,
    reset:      0,  // Don't create new entry
    is_complete: 1  // Mark as complete
  });

  fetch("../student/save-progress.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params.toString()
  })
    .then(r => r.json())
    .then(data => {
      if (!data.success) console.error("Save failed:", data.message);
      else {
        console.log("Final progress saved:", data.message);
        // Clear game state for next game
        localStorage.removeItem(`game_${QUIZ_ID}_started`);
        sessionStorage.removeItem(`game_${QUIZ_ID}_entry_created`);
        
        // Mark this specific quiz as completed with quiz-specific key
        localStorage.setItem(`completed_quiz_${QUIZ_ID}`, 'true');
        
        // Also clear the server-side game progress session for this specific quiz
        fetch("../student/clear-game-session.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({ quiz_id: QUIZ_ID }).toString()
        }).catch(err => console.error("Error clearing game session:", err));
        
        // Reset
        score = 0;
        p1sum = 0;
        updateScoreDisplay();

        // Move back to start
        const token = document.getElementById("p1");
        document.getElementById("b01").querySelector(".tokens").appendChild(token);

        // Show overlay
        const overlay = document.getElementById("endGameOverlay");
        overlay.style.display = "flex";
        document.getElementById("goHomeBtn").onclick = () =>
          window.location.href = "../student/student-homepage.php?new_game=1&completed_quiz=" + QUIZ_ID;
        document.getElementById("newGameBtn").onclick = () =>
          window.location.href = `javaquest.php?new=1&quiz_id=${QUIZ_ID}`;
      }
    })
    .catch(err => console.error("Error saving progress:", err));
}

function movePlayerAnimated(finalPosition) {
  const token = document.getElementById("p1");
  const path = [];
  for (let i = p1sum + 1; i <= finalPosition; i++) path.push(i);

  let idx = 0;
  function step() {
    if (idx < path.length) {
      const pos = path[idx++];
      const tile = document.getElementById(pos < 10 ? `b0${pos}` : `b${pos}`);
      tile.querySelector('.tokens').appendChild(token);
      p1sum = pos;
      updateScoreDisplay();
      saveProgressToDatabase();
      setTimeout(step, 200);
    } else {
      if (jumps[p1sum]) {
        const target = jumps[p1sum];
        setTimeout(() => {
          snakePaths[p1sum]
            ? slideThroughPath(p1sum, snakePaths[p1sum])
            : slideToTarget(p1sum, target);
        }, 400);
      } else if (p1sum === 100) {
        handleWin();
      }
    }
  }
  step();
}

function slideToTarget(from, to) {
  const token = document.getElementById("p1");
  const board = document.querySelector(".cont");
  const fromTile = document.getElementById(from < 10 ? `b0${from}` : `b${from}`);
  const toTile   = document.getElementById(to   < 10 ? `b0${to}`   : `b${to}`);

  const br = board.getBoundingClientRect();
  const fr = fromTile.getBoundingClientRect();
  const tr = toTile.getBoundingClientRect();

  const startX = fr.left - br.left + fr.width/2;
  const startY = fr.top  - br.top  + fr.height/2;
  const endX   = tr.left - br.left + tr.width/2;
  const endY   = tr.top  - br.top  + tr.height/2;

  board.appendChild(token);
  Object.assign(token.style, {
    transition: "none",
    left:       `${startX}px`,
    top:        `${startY}px`,
    transform:  "translate(-50%, -50%)"
  });

  setTimeout(() => {
    token.style.transition = "left 0.8s ease, top 0.8s ease";
    token.style.left = `${endX}px`;
    token.style.top  = `${endY}px`;
    setTimeout(() => {
      token.style.transition = "none";
      token.style.left = token.style.top = "";
      toTile.querySelector(".tokens").appendChild(token);
      p1sum = to;
      updateScoreDisplay();
      saveProgressToDatabase();
      if (p1sum === 100) handleWin();
    }, 900);
  }, 10);
}

function slideThroughPath(from, path) {
  const token = document.getElementById("p1");
  const board = document.querySelector(".cont");
  const tiles = [from, ...path];
  let idx = 0;

  function next() {
    if (idx >= tiles.length - 1) return;
    const a = tiles[idx], b = tiles[++idx];
    const aTile = document.getElementById(a < 10 ? `b0${a}` : `b${a}`);
    const bTile = document.getElementById(b < 10 ? `b0${b}` : `b${b}`);
    const br    = board.getBoundingClientRect();
    const ar    = aTile.getBoundingClientRect();
    const br2   = bTile.getBoundingClientRect();

    const startX = ar.left - br.left + ar.width/2;
    const startY = ar.top  - br.top  + ar.height/2;
    const endX   = br2.left - br.left + br2.width/2;
    const endY   = br2.top  - br.top  + br2.height/2;

    board.appendChild(token);
    Object.assign(token.style, {
      transition: "none",
      left:       `${startX}px`,
      top:        `${startY}px`
    });

    setTimeout(() => {
      token.style.transition = "left 0.4s ease, top 0.4s ease";
      token.style.left = `${endX}px`;
      token.style.top  = `${endY}px`;
      setTimeout(next, 450);
    }, 10);
  }

  next();

  // After path, snap to final
  setTimeout(() => {
    const last = path[path.length - 1];
    const finalTile = document.getElementById(last < 10 ? `b0${last}` : `b${last}`);
    finalTile.querySelector(".tokens").appendChild(token);
    Object.assign(token.style, { transition: "none", left: "", top: "" });
    p1sum = last;
    updateScoreDisplay();
    saveProgressToDatabase();
    if (p1sum === 100) handleWin();
  }, path.length * 500);
}

function play(roll) {
  const target = p1sum + roll;
  if (target > 100) {
    alert("Roll exceeds 100. Stay in place.");
  } else {
    movePlayerAnimated(target);
  }
}

function loadQuestion() {
  fetch(`../student/get-question.php?quiz_id=${QUIZ_ID}`)
    .then(r => r.json())
    .then(data => {
      if (data.error) return alert(data.error);
      document.getElementById("questionText").innerText = data.question_text;
      document.getElementById("popupOverlay").dataset.qid = data.question_id;
      const opts = document.getElementById("optionsContainer");
      opts.innerHTML = "";
      ['Option 1','Option 2','Option 3','Option 4'].forEach(key => {
        if (data[key]) {
          const lbl = document.createElement("label");
          lbl.innerHTML = `<input type="radio" name="question" value="${data[key]}"> ${data[key]}`;
          opts.appendChild(lbl);
        }
      });
    })
    .catch(console.error);
}

function submitChoice() {
  const sel = [...document.getElementsByName("question")].find(r => r.checked)?.value;
  if (!sel) return alert("Please select an answer!");
  const qid = document.getElementById("popupOverlay").dataset.qid;
  document.getElementById("popupOverlay").style.display = "none";

  fetch("../student/check-answer.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({
      question_id:      qid,
      selected_answer:  sel
    }).toString()
  })
    .then(r => r.json())
    .then(data => {
      if (data.correct) {
        alert("Correct!");
        score += 10;
      } else {
        alert("Incorrect! 5 points deducted.");
        score = Math.max(0, score - 5);
      }
      updateScoreDisplay();
      saveProgressToDatabase();
      if (data.correct) play(num);
    })
    .catch(console.error);
}

window.addEventListener("DOMContentLoaded", () => {
  // Check if URL parameter contains new=1, if so, force create a new game
  const urlParams = new URLSearchParams(window.location.search);
  const isNewGame = urlParams.get('new') === '1';
  
  // Check if this specific quiz was previously completed
  const wasCompleted = localStorage.getItem(`completed_quiz_${QUIZ_ID}`) === 'true';
  
  // Treat as new game if explicitly requested or if this quiz was previously completed
  if (isNewGame || wasCompleted) {
    // If this was previously completed, but no new=1 parameter, add it to URL
    if (wasCompleted && !isNewGame) {
      urlParams.set('new', '1');
      const newUrl = window.location.pathname + '?' + urlParams.toString();
      window.history.replaceState({}, document.title, newUrl);
    }
    
    // Mark this as a new game - clear any previous game state
    localStorage.removeItem(`game_${QUIZ_ID}_started`);
    
    // If this specific quiz was completed, clear its completion status
    if (localStorage.getItem(`completed_quiz_${QUIZ_ID}`) === 'true') {
      localStorage.removeItem(`completed_quiz_${QUIZ_ID}`);
    }
    
    // Reset game state
    score = 0;
    p1sum = 0;
    updateScoreDisplay();
    
    // For a brand new game, we'll only create database entry when player actually rolls
    playerHasRolled = false;
    
    // Position player at start
    const startTile = document.getElementById('b01');
    if (startTile) {
      startTile.querySelector(".tokens").appendChild(document.getElementById("p1"));
    }
    
    // Set up game controls
    setupGameControls();
    
    // Now that we've processed the new game state, remove the 'new' parameter
    if (isNewGame) {
      urlParams.delete('new');
      const cleanUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
      window.history.replaceState({}, document.title, cleanUrl);
    }
    
    return;
  }
  
  // For continuing games, load the saved state
  fetch(`../student/load-progress.php?quiz_id=${QUIZ_ID}`)
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        // Continue existing game
        score = data.score || 0;
        p1sum = data.lst_played || 0;
        // If we're loading a game with progress, player has definitely rolled
        if (p1sum > 0 || score > 0) {
          playerHasRolled = true;
        }
        
        // If the loaded position is 100, this was a completed game, so start over
        if (p1sum === 100) {
          console.log("Detected completed game at position 100, resetting to new game");
          localStorage.removeItem(`game_${QUIZ_ID}_started`);
          score = 0;
          p1sum = 0;
          playerHasRolled = false;
          // Force a database reset on next roll
          localStorage.setItem(QUIZ_ID + "_needs_reset", "true");
        }
      } else {
        // No saved progress, don't mark as played yet
        score = 0;
        p1sum = 0;
        playerHasRolled = false;
      }
      
      updateScoreDisplay();
      const startTile = document.getElementById(
        p1sum < 10 ? `b0${p1sum}` : `b${p1sum}`
      );
      if (startTile) {
        startTile.querySelector(".tokens")
                .appendChild(document.getElementById("p1"));
      } else {
        // Fallback to position 1 if there's an issue
        document.getElementById("b01").querySelector(".tokens")
                .appendChild(document.getElementById("p1"));
      }
      
      setupGameControls();
    })
    .catch(error => {
      console.error("Error loading game:", error);
      // Fall back to new game on error
      score = 0;
      p1sum = 0;
      playerHasRolled = false;
      updateScoreDisplay();
      setupGameControls();
    });
});

// Helper function to set up the game controls
function setupGameControls() {
  // Roll button
  document.getElementById("diceBtn").addEventListener("click", () => {
    rollingSound.play();
    num = Math.floor(Math.random() * 6) + 1;
    document.getElementById("dice").innerText = num;
    
    // Mark that player has started playing
    playerHasRolled = true;
    
    // Check if we need to reset the game in database
    if (localStorage.getItem(QUIZ_ID + "_needs_reset") === "true") {
      localStorage.removeItem(QUIZ_ID + "_needs_reset");
      saveProgressToDatabase(true); // Force reset
    }
    
    loadQuestion();
    document.getElementById("popupOverlay").style.display = "flex";
  });

  // The quiz popup already has an onclick handler in the HTML
  // <button onclick="submitChoice()" type="button">Submit</button>
}
