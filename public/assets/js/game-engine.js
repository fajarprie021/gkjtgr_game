/**
 * Game Engine - Unified system for all game mechanics
 * Iteration 9: Game Engine & Mechanics Expansion
 */

// Game Type Registry
const GAME_TYPES = {
  MULTIPLE_CHOICE: "multiple_choice",
  TRUE_FALSE: "true_false",
  SEQUENCE: "sequence",
  MATCHING: "matching",
  TIMELINE: "timeline",
  VERSE_PUZZLE: "verse_puzzle",
};

const GameEngine = {
  state: {
    questions: [],
    currentQuestionIndex: 0,
    storyId: null,
    classGroup: null,
    mode: "solo", // solo or classroom
    sessionId: null,
    currentAnswer: null,
  },

  // Initialize game engine
  async init(storyId, classGroup, mode = "solo", sessionId = null) {
    this.state.storyId = storyId;
    this.state.classGroup = classGroup;
    this.state.mode = mode;
    this.state.sessionId = sessionId;
    this.state.questions = await ApiService.getQuestions(storyId, classGroup);
    this.state.currentQuestionIndex = 0;

    if (!this.state.questions || this.state.questions.length === 0) {
      throw new Error("No questions available");
    }

    ApiService.logAnalyticsEvent({
      event_type: "story_started",
      story_id: storyId,
      class_group: classGroup,
      game_mode: mode,
      session_id: sessionId,
      metadata: { question_count: this.state.questions.length },
    });

    this.renderQuestion();
  },

  // Main render dispatcher
  renderQuestion() {
    const question = this.state.questions[this.state.currentQuestionIndex];

    if (!question) {
      console.error("Question not found");
      return;
    }

    // Validate question data
    if (!this.validateQuestionData(question)) {
      this.showError("Tantangan ini belum dapat dimuat.");
      return;
    }

    const container = document.getElementById("app");

    // Render header
    const header = this.renderHeader();

    // Select renderer based on question type
    let questionContent;
    try {
      questionContent = this.selectRenderer(question);
    } catch (error) {
      console.error("Renderer error:", error);
      this.showError("Tipe tantangan tidak didukung.");
      return;
    }

    container.innerHTML = `
            <div class="question-screen">
                ${header}
                ${questionContent}
            </div>
        `;

    // Initialize mechanic-specific interactions
    this.initMechanicInteraction(question.type);
  },

  // Render common header
  renderHeader() {
    const progress = (
      (this.state.currentQuestionIndex / this.state.questions.length) *
      100
    ).toFixed(0);

    return `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold" style="color: var(--game-primary);">
                    <i class="bi bi-star-fill"></i> Misi: ${App.state.currentStory?.title || "Story"}
                </span>
                <span class="badge" style="background: var(--game-secondary); font-size: 0.9rem;">
                    ${this.state.currentQuestionIndex + 1} / ${this.state.questions.length}
                </span>
            </div>
            
            <div class="mission-progress mb-4">
                <div class="mission-progress-bar" style="width: ${progress}%"></div>
            </div>
        `;
  },

  // Renderer selector
  selectRenderer(question) {
    const renderers = {
      [GAME_TYPES.MULTIPLE_CHOICE]: () => this.renderMultipleChoice(question),
      [GAME_TYPES.TRUE_FALSE]: () => this.renderTrueFalse(question),
      [GAME_TYPES.SEQUENCE]: () => this.renderSequence(question),
      [GAME_TYPES.MATCHING]: () => this.renderMatching(question),
      [GAME_TYPES.TIMELINE]: () => this.renderTimeline(question),
      [GAME_TYPES.VERSE_PUZZLE]: () => this.renderVersePuzzle(question),
    };

    const renderer = renderers[question.type];

    if (!renderer) {
      throw new Error(`Unsupported question type: ${question.type}`);
    }

    return renderer();
  },

  // Validate question data
  validateQuestionData(question) {
    if (!question.type || !question.question) {
      return false;
    }

    // Type-specific validation
    switch (question.type) {
      case GAME_TYPES.MULTIPLE_CHOICE:
        return question.options && question.options.length >= 2;
      case GAME_TYPES.TRUE_FALSE:
        return true;
      case GAME_TYPES.SEQUENCE:
        return question.items && question.items.length >= 2;
      case GAME_TYPES.MATCHING:
        return (
          question.leftItems &&
          question.rightItems &&
          question.leftItems.length >= 2
        );
      case GAME_TYPES.TIMELINE:
        return question.items && question.items.length >= 2;
      case GAME_TYPES.VERSE_PUZZLE:
        return question.items && question.items.length >= 2;
      default:
        return false;
    }
  },

  // ===== RENDERERS =====

  // 1. Multiple Choice Renderer
  renderMultipleChoice(question) {
    const optionLabels = ["A", "B", "C", "D", "E"];
    const options = question.options || [];

    return `
            <div class="question-card game-card p-4 mb-4">
                <h4 class="mb-4" style="color: var(--game-text); line-height: 1.5;">
                    ${question.question}
                </h4>
                
                <div class="options-container">
                    ${options
                      .map(
                        (opt, i) => `
                        <button class="answer-option" data-answer="${this.escapeHtml(opt)}">
                            <span class="answer-option-label">${optionLabels[i]}</span>
                            <span>${this.escapeHtml(opt)}</span>
                        </button>
                    `,
                      )
                      .join("")}
                </div>
            </div>
        `;
  },

  // 2. True/False Renderer
  renderTrueFalse(question) {
    return `
            <div class="question-card game-card p-4 mb-4">
                <h4 class="mb-4" style="color: var(--game-text); line-height: 1.5;">
                    ${question.question}
                </h4>
                
                <div class="true-false-container row g-3">
                    <div class="col-6">
                        <button class="true-false-btn true-btn" data-answer="true">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>BENAR</span>
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="true-false-btn false-btn" data-answer="false">
                            <i class="bi bi-x-circle-fill"></i>
                            <span>SALAH</span>
                        </button>
                    </div>
                </div>
            </div>
        `;
  },

  // 3. Sequence Renderer
  renderSequence(question) {
    const items = question.items || [];
    // Shuffle items for display
    const shuffled = this.shuffleArray([...items]);

    return `
            <div class="question-card game-card p-4 mb-4">
                <h4 class="mb-3" style="color: var(--game-text);">
                    ${question.question || "Susun urutan yang benar:"}
                </h4>
                <p class="text-muted small mb-4">
                    <i class="bi bi-info-circle"></i> Gunakan tombol ↑↓ untuk mengatur urutan
                </p>
                
                <div class="sequence-container" id="sequenceContainer">
                    ${shuffled
                      .map(
                        (item, index) => `
                        <div class="sequence-item" data-item-id="${item.id || index}">
                            <span class="sequence-number">${index + 1}</span>
                            <span class="sequence-text">${this.escapeHtml(item.text || item)}</span>
                            <div class="sequence-controls">
                                <button class="sequence-btn" onclick="GameEngine.moveSequenceItem(${index}, -1)" ${index === 0 ? "disabled" : ""}>
                                    <i class="bi bi-arrow-up"></i>
                                </button>
                                <button class="sequence-btn" onclick="GameEngine.moveSequenceItem(${index}, 1)" ${index === shuffled.length - 1 ? "disabled" : ""}>
                                    <i class="bi bi-arrow-down"></i>
                                </button>
                            </div>
                        </div>
                    `,
                      )
                      .join("")}
                </div>
                
                <button class="game-btn game-btn-primary mt-3 w-100" onclick="GameEngine.submitSequence()">
                    <i class="bi bi-check-circle"></i> PERIKSA URUTAN
                </button>
            </div>
        `;
  },

  // 4. Matching Renderer
  renderMatching(question) {
    const leftItems = question.leftItems || [];
    const rightItems = this.shuffleArray([...(question.rightItems || [])]);

    return `
            <div class="question-card game-card p-4 mb-4">
                <h4 class="mb-3" style="color: var(--game-text);">
                    ${question.question || "Cocokkan pasangan yang tepat:"}
                </h4>
                <p class="text-muted small mb-4">
                    <i class="bi bi-info-circle"></i> Tap item kiri, lalu tap pasangannya di kanan
                </p>
                
                <div class="matching-container row">
                    <div class="col-6">
                        <div class="matching-column">
                            ${leftItems
                              .map(
                                (item, index) => `
                                <div class="matching-item matching-left" data-item-id="${item.id || index}" data-side="left">
                                    <span>${this.escapeHtml(item.text || item)}</span>
                                </div>
                            `,
                              )
                              .join("")}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="matching-column">
                            ${rightItems
                              .map(
                                (item, index) => `
                                <div class="matching-item matching-right" data-item-id="${item.id || index}" data-side="right">
                                    <span>${this.escapeHtml(item.text || item)}</span>
                                </div>
                            `,
                              )
                              .join("")}
                        </div>
                    </div>
                </div>
                
                <div id="matchingPairs" class="mt-3 text-center text-muted small">
                    Belum ada pasangan
                </div>
                
                <button class="game-btn game-btn-primary mt-3 w-100" onclick="GameEngine.submitMatching()">
                    <i class="bi bi-check-circle"></i> PERIKSA JAWABAN
                </button>
            </div>
        `;
  },

  // 5. Timeline Renderer
  renderTimeline(question) {
    const items = question.items || [];
    const shuffled = this.shuffleArray([...items]);

    return `
            <div class="question-card game-card p-4 mb-4">
                <h4 class="mb-3" style="color: var(--game-text);">
                    ${question.question || "Susun urutan waktu yang benar:"}
                </h4>
                <p class="text-muted small mb-4">
                    <i class="bi bi-clock-history"></i> Urutkan dari yang paling awal terjadi
                </p>
                
                <div class="timeline-container" id="timelineContainer">
                    ${shuffled
                      .map(
                        (item, index) => `
                        <div class="timeline-item" data-item-id="${item.id || item.story_id || index}">
                            <span class="timeline-number">${index + 1}</span>
                            <span class="timeline-text">${this.escapeHtml(item.text || item.title || item)}</span>
                            <div class="timeline-controls">
                                <button class="timeline-btn" onclick="GameEngine.moveTimelineItem(${index}, -1)" ${index === 0 ? "disabled" : ""}>
                                    <i class="bi bi-arrow-up"></i>
                                </button>
                                <button class="timeline-btn" onclick="GameEngine.moveTimelineItem(${index}, 1)" ${index === shuffled.length - 1 ? "disabled" : ""}>
                                    <i class="bi bi-arrow-down"></i>
                                </button>
                            </div>
                        </div>
                    `,
                      )
                      .join("")}
                </div>
                
                <button class="game-btn game-btn-primary mt-3 w-100" onclick="GameEngine.submitTimeline()">
                    <i class="bi bi-check-circle"></i> PERIKSA URUTAN
                </button>
            </div>
        `;
  },

  // 6. Verse Puzzle Renderer
  renderVersePuzzle(question) {
    const items = question.items || [];
    const shuffled = this.shuffleArray([...items]);
    const reference = question.reference || "";

    return `
            <div class="question-card game-card p-4 mb-4">
                <h4 class="mb-3" style="color: var(--game-text);">
                    ${question.question || "Susun ayat dengan benar:"}
                </h4>
                ${reference ? `<p class="text-muted mb-3"><i class="bi bi-book"></i> ${this.escapeHtml(reference)}</p>` : ""}
                <p class="text-muted small mb-4">
                    <i class="bi bi-info-circle"></i> Susun kata-kata ayat sesuai urutan yang benar
                </p>
                
                <div class="verse-container" id="verseContainer">
                    ${shuffled
                      .map(
                        (item, index) => `
                        <div class="verse-word" data-word-id="${item.id || index}">
                            <span class="verse-number">${index + 1}</span>
                            <span class="verse-text">${this.escapeHtml(item.text || item)}</span>
                            <div class="verse-controls">
                                <button class="verse-btn" onclick="GameEngine.moveVerseWord(${index}, -1)" ${index === 0 ? "disabled" : ""}>
                                    <i class="bi bi-arrow-left"></i>
                                </button>
                                <button class="verse-btn" onclick="GameEngine.moveVerseWord(${index}, 1)" ${index === shuffled.length - 1 ? "disabled" : ""}>
                                    <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    `,
                      )
                      .join("")}
                </div>
                
                <button class="game-btn game-btn-primary mt-3 w-100" onclick="GameEngine.submitVerse()">
                    <i class="bi bi-check-circle"></i> PERIKSA AYAT
                </button>
            </div>
        `;
  },

  // ===== MECHANIC INTERACTIONS =====

  initMechanicInteraction(type) {
    switch (type) {
      case GAME_TYPES.MULTIPLE_CHOICE:
        this.initMultipleChoice();
        break;
      case GAME_TYPES.TRUE_FALSE:
        this.initTrueFalse();
        break;
      case GAME_TYPES.MATCHING:
        this.initMatching();
        break;
      // Sequence, Timeline, Verse use button interactions (already bound)
    }
  },

  initMultipleChoice() {
    document.querySelectorAll(".answer-option").forEach((btn) => {
      btn.addEventListener("click", () => {
        const answer = btn.dataset.answer;
        this.submitAnswer(answer);
      });
    });
  },

  initTrueFalse() {
    document.querySelectorAll(".true-false-btn").forEach((btn) => {
      btn.addEventListener("click", () => {
        const answer = btn.dataset.answer === "true";
        this.submitAnswer(answer);
      });
    });
  },

  initMatching() {
    this.matchingState = { pairs: [], selectedLeft: null, selectedRight: null };

    document.querySelectorAll(".matching-item").forEach((item) => {
      item.addEventListener("click", () => {
        const side = item.dataset.side;
        const itemId = item.dataset.itemId;

        if (side === "left") {
          // Deselect previous
          document
            .querySelectorAll(".matching-left")
            .forEach((el) => el.classList.remove("selected"));
          item.classList.add("selected");
          this.matchingState.selectedLeft = itemId;
        } else {
          document
            .querySelectorAll(".matching-right")
            .forEach((el) => el.classList.remove("selected"));
          item.classList.add("selected");
          this.matchingState.selectedRight = itemId;
        }

        // If both selected, create pair
        if (
          this.matchingState.selectedLeft &&
          this.matchingState.selectedRight
        ) {
          this.matchingState.pairs.push([
            this.matchingState.selectedLeft,
            this.matchingState.selectedRight,
          ]);

          // Mark as paired
          document.querySelectorAll(".matching-item.selected").forEach((el) => {
            el.classList.remove("selected");
            el.classList.add("paired");
          });

          this.matchingState.selectedLeft = null;
          this.matchingState.selectedRight = null;

          this.updateMatchingDisplay();
        }
      });
    });
  },

  updateMatchingDisplay() {
    const display = document.getElementById("matchingPairs");
    if (this.matchingState.pairs.length === 0) {
      display.innerHTML = "Belum ada pasangan";
    } else {
      display.innerHTML = `<i class="bi bi-check-circle text-success"></i> ${this.matchingState.pairs.length} pasangan dibuat`;
    }
  },

  // Move items for sequence/timeline/verse
  moveSequenceItem(index, direction) {
    const container = document.getElementById("sequenceContainer");
    const items = Array.from(container.children);
    const newIndex = index + direction;

    if (newIndex >= 0 && newIndex < items.length) {
      if (direction === -1) {
        container.insertBefore(items[index], items[newIndex]);
      } else {
        container.insertBefore(items[newIndex], items[index]);
      }
      this.updateSequenceNumbers();
    }
  },

  moveTimelineItem(index, direction) {
    const container = document.getElementById("timelineContainer");
    const items = Array.from(container.children);
    const newIndex = index + direction;

    if (newIndex >= 0 && newIndex < items.length) {
      if (direction === -1) {
        container.insertBefore(items[index], items[newIndex]);
      } else {
        container.insertBefore(items[newIndex], items[index]);
      }
      this.updateTimelineNumbers();
    }
  },

  moveVerseWord(index, direction) {
    const container = document.getElementById("verseContainer");
    const items = Array.from(container.children);
    const newIndex = index + direction;

    if (newIndex >= 0 && newIndex < items.length) {
      if (direction === -1) {
        container.insertBefore(items[index], items[newIndex]);
      } else {
        container.insertBefore(items[newIndex], items[index]);
      }
      this.updateVerseNumbers();
    }
  },

  updateSequenceNumbers() {
    document.querySelectorAll(".sequence-item").forEach((item, index) => {
      item.querySelector(".sequence-number").textContent = index + 1;
      const upBtn = item.querySelectorAll(".sequence-btn")[0];
      const downBtn = item.querySelectorAll(".sequence-btn")[1];
      upBtn.disabled = index === 0;
      downBtn.disabled =
        index === document.querySelectorAll(".sequence-item").length - 1;
      upBtn.setAttribute(
        "onclick",
        `GameEngine.moveSequenceItem(${index}, -1)`,
      );
      downBtn.setAttribute(
        "onclick",
        `GameEngine.moveSequenceItem(${index}, 1)`,
      );
    });
  },

  updateTimelineNumbers() {
    document.querySelectorAll(".timeline-item").forEach((item, index) => {
      item.querySelector(".timeline-number").textContent = index + 1;
      const upBtn = item.querySelectorAll(".timeline-btn")[0];
      const downBtn = item.querySelectorAll(".timeline-btn")[1];
      upBtn.disabled = index === 0;
      downBtn.disabled =
        index === document.querySelectorAll(".timeline-item").length - 1;
      upBtn.setAttribute(
        "onclick",
        `GameEngine.moveTimelineItem(${index}, -1)`,
      );
      downBtn.setAttribute(
        "onclick",
        `GameEngine.moveTimelineItem(${index}, 1)`,
      );
    });
  },

  updateVerseNumbers() {
    document.querySelectorAll(".verse-word").forEach((item, index) => {
      item.querySelector(".verse-number").textContent = index + 1;
      const leftBtn = item.querySelectorAll(".verse-btn")[0];
      const rightBtn = item.querySelectorAll(".verse-btn")[1];
      leftBtn.disabled = index === 0;
      rightBtn.disabled =
        index === document.querySelectorAll(".verse-word").length - 1;
      leftBtn.setAttribute("onclick", `GameEngine.moveVerseWord(${index}, -1)`);
      rightBtn.setAttribute("onclick", `GameEngine.moveVerseWord(${index}, 1)`);
    });
  },

  // Submit methods for complex mechanics
  submitSequence() {
    const container = document.getElementById("sequenceContainer");
    const items = Array.from(container.children);
    const answer = items.map((item) => item.dataset.itemId);
    this.submitAnswer(answer);
  },

  submitMatching() {
    const answer = this.matchingState.pairs;
    if (answer.length === 0) {
      alert("Buat minimal satu pasangan terlebih dahulu");
      return;
    }
    this.submitAnswer(answer);
  },

  submitTimeline() {
    const container = document.getElementById("timelineContainer");
    const items = Array.from(container.children);
    const answer = items.map((item) => item.dataset.itemId);
    this.submitAnswer(answer);
  },

  submitVerse() {
    const container = document.getElementById("verseContainer");
    const items = Array.from(container.children);
    const answer = items.map((item) => item.dataset.wordId);
    this.submitAnswer(answer);
  },

  // ===== ANSWER SUBMISSION =====

  async submitAnswer(answer) {
    const question = this.state.questions[this.state.currentQuestionIndex];

    ApiService.logAnalyticsEvent({
      event_type: "answer_submitted",
      story_id: this.state.storyId,
      question_id: question.id,
      question_type: question.type,
      class_group: this.state.classGroup,
      game_mode: this.state.mode,
      session_id: this.state.sessionId,
    });

    try {
      const result = await ApiService.validateAnswer(question.id, answer);
      ApiService.logAnalyticsEvent({
        event_type: "question_completed",
        story_id: this.state.storyId,
        question_id: question.id,
        question_type: question.type,
        class_group: this.state.classGroup,
        game_mode: this.state.mode,
        session_id: this.state.sessionId,
        result: result.correct ? "correct" : "wrong",
      });
      this.showFeedback(result.correct, result.feedback || result.message);
    } catch (error) {
      console.error("Answer validation error:", error);
      ApiService.logAnalyticsEvent({
        event_type: "technical_error",
        story_id: this.state.storyId,
        question_id: question.id,
        result: "error",
        metadata: { source: "validateAnswer" },
      });
      this.showFeedback(false, "Terjadi kesalahan. Silakan coba lagi.");
    }
  },

  // ===== FEEDBACK =====

  showFeedback(isCorrect, message) {
    const overlay = document.createElement("div");
    overlay.className = "feedback-overlay";

    overlay.innerHTML = `
            <div class="feedback-content">
                <div class="feedback-icon">
                    <i class="bi ${isCorrect ? "bi-check-circle-fill" : "bi-x-circle-fill"}" 
                       style="color: ${isCorrect ? "var(--game-success)" : "var(--game-danger)"};">
                    </i>
                </div>
                <h2 style="color: ${isCorrect ? "var(--game-success)" : "var(--game-danger)"};">
                    ${isCorrect ? "Hebat!" : "Belum Tepat"}
                </h2>
                <p>${this.escapeHtml(message)}</p>
                ${!isCorrect ? '<p class="text-muted small"><i class="bi bi-lightbulb"></i> Pikirkan lagi dengan teliti ya!</p>' : ""}
                <button class="game-btn ${isCorrect ? "game-btn-primary" : "game-btn-accent"}" onclick="GameEngine.closeFeedback(${isCorrect})">
                    ${isCorrect ? "<i class='bi bi-arrow-right-circle-fill'></i> LANJUT" : "<i class='bi bi-arrow-clockwise'></i> COBA LAGI"}
                </button>
            </div>
        `;

    document.body.appendChild(overlay);
  },

  closeFeedback(isCorrect) {
    document.querySelector(".feedback-overlay")?.remove();

    if (isCorrect) {
      this.state.currentQuestionIndex++;
      if (this.state.currentQuestionIndex < this.state.questions.length) {
        this.renderQuestion();
      } else {
        this.finishMission();
      }
    }
  },

  // ===== MISSION COMPLETION =====

  async finishMission() {
    // Mark story as completed
    ProgressService.completeStory(this.state.storyId);

    ApiService.logAnalyticsEvent({
      event_type: "story_completed",
      story_id: this.state.storyId,
      class_group: this.state.classGroup,
      game_mode: this.state.mode,
      session_id: this.state.sessionId,
      result: "completed",
    });

    // Get story content
    const content = await ApiService.getStoryContent(
      this.state.storyId,
      this.state.classGroup,
    );
    const learningValue =
      content?.main_message ||
      "Tuhan mengajarkan kita hal-hal penting melalui cerita ini.";

    // Check timeline
    const timeline = ProgressService.getTimelineContext(
      this.state.storyId,
      App.state.stories,
    );

    const container = document.getElementById("app");
    container.innerHTML = `
            <div class="mission-complete">
                <div class="celebration-icon">
                    <i class="bi bi-trophy-fill" style="color: var(--game-accent);"></i>
                </div>
                <h1>MISI SELESAI!</h1>
                <p class="lead" style="color: var(--game-text);">
                    Luar biasa! Kamu telah menyelesaikan cerita <strong>${App.state.currentStory.title}</strong>!
                </p>
                
                <div class="game-card p-4 my-4" style="background: linear-gradient(135deg, #f0f4c3 0%, #dcedc8 100%); border: 3px solid var(--game-success);">
                    <h5 style="color: var(--game-primary);">
                        <i class="bi bi-book-half"></i> Apa yang kita pelajari?
                    </h5>
                    <p style="font-size: 1.05rem; line-height: 1.7; margin-bottom: 0;">
                        ${this.escapeHtml(learningValue)}
                    </p>
                </div>

                ${
                  timeline?.next
                    ? `
                <div class="alert alert-info">
                    <i class="bi bi-arrow-right-circle-fill"></i>
                    <strong>Cerita Selanjutnya:</strong> ${this.escapeHtml(timeline.next.title)}
                </div>
                `
                    : ""
                }

                <button class="game-btn game-btn-primary" onclick="App.showMap()">
                    <i class="bi bi-map-fill"></i> KEMBALI KE MAP
                </button>
            </div>
        `;
  },

  // ===== UTILITIES =====

  shuffleArray(array) {
    const shuffled = [...array];
    for (let i = shuffled.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
    }
    return shuffled;
  },

  escapeHtml(text) {
    if (!text) return "";
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
  },

  showError(message) {
    const container = document.getElementById("app");
    container.innerHTML = `
            <div class="text-center py-5">
                <i class="bi bi-exclamation-triangle display-1 text-warning mb-3"></i>
                <h3>${this.escapeHtml(message)}</h3>
                <button class="game-btn game-btn-primary mt-3" onclick="App.showMap()">
                    <i class="bi bi-arrow-left"></i> Kembali ke Map
                </button>
            </div>
        `;
  },
};

// Backward compatibility - map old Game object to GameEngine
const Game = {
  startMisi: (storyId, classGroup) =>
    GameEngine.init(storyId, classGroup, "solo"),
  closeFeedback: (isCorrect) => GameEngine.closeFeedback(isCorrect),
};
