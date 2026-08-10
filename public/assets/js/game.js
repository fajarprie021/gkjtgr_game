/**
 * Game Mechanic Logic
 */

const Game = {
  state: {
    questions: [],
    currentQuestionIndex: 0,
    storyId: null,
    classGroup: null,
  },

  async startMisi(storyId, classGroup) {
    this.state.storyId = storyId;
    this.state.classGroup = classGroup;
    this.state.questions = await ApiService.getQuestions(storyId, classGroup);
    this.state.currentQuestionIndex = 0;

    this.renderQuestion();
  },

  renderQuestion() {
    const question = this.state.questions[this.state.currentQuestionIndex];
    const container = document.getElementById("app");

    const optionLabels = ["A", "B", "C", "D"];

    container.innerHTML = `
            <div class="question-screen">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold" style="color: var(--game-primary);">
                        <i class="bi bi-star-fill"></i> Misi: ${App.state.currentStory.title}
                    </span>
                    <span class="badge" style="background: var(--game-secondary); font-size: 0.9rem;">
                        ${this.state.currentQuestionIndex + 1} / ${this.state.questions.length}
                    </span>
                </div>
                
                <div class="mission-progress mb-4">
                    <div class="mission-progress-bar" style="width: ${(this.state.currentQuestionIndex / this.state.questions.length) * 100}%"></div>
                </div>

                <div class="question-card game-card p-4 mb-4">
                    <h4 class="mb-4" style="color: var(--game-text); line-height: 1.5;">${question.question}</h4>
                    
                    <div class="options-container">
                        ${question.options
                          .map(
                            (opt, i) => `
                            <button class="answer-option" onclick="Game.checkAnswer('${opt}')">
                                <span class="answer-option-label">${optionLabels[i]}</span>
                                <span>${opt}</span>
                            </button>
                        `,
                          )
                          .join("")}
                    </div>
                </div>
            </div>
        `;
  },

  async checkAnswer(selectedOption) {
    const question = this.state.questions[this.state.currentQuestionIndex];

    try {
      const result = await ApiService.validateAnswer(
        question.id,
        selectedOption,
      );
      this.showFeedback(result.correct, result.feedback);
    } catch (error) {
      console.error("Answer validation error:", error);
      this.showFeedback(false, "Terjadi kesalahan. Silakan coba lagi.");
    }
  },

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
                <p>${message}</p>
                ${!isCorrect ? '<p class="text-muted small"><i class="bi bi-lightbulb"></i> Bacalah cerita dengan lebih teliti ya!</p>' : ""}
                <button class="game-btn ${isCorrect ? "game-btn-primary" : "game-btn-accent"}" onclick="Game.closeFeedback(${isCorrect})">
                    ${isCorrect ? "<i class='bi bi-arrow-right-circle-fill'></i> LANJUT" : "<i class='bi bi-arrow-clockwise'></i> COBA LAGI"}
                </button>
            </div>
        `;

    document.body.appendChild(overlay);
  },

  closeFeedback(isCorrect) {
    document.querySelector(".feedback-overlay").remove();

    if (isCorrect) {
      this.state.currentQuestionIndex++;
      if (this.state.currentQuestionIndex < this.state.questions.length) {
        this.renderQuestion();
      } else {
        this.finishMisi();
      }
    }
  },

  async finishMisi() {
    // Mark story as completed
    ProgressService.completeStory(this.state.storyId);

    // Get story content for learning value
    const content = await ApiService.getStoryContent(
      this.state.storyId,
      this.state.classGroup,
    );
    const learningValue =
      content?.main_message ||
      "Tuhan mengajarkan kita hal-hal penting melalui cerita ini.";

    // Check what's next
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
                        ${learningValue}
                    </p>
                </div>

                ${
                  timeline?.next
                    ? `
                <div class="alert alert-info">
                    <i class="bi bi-arrow-right-circle-fill"></i>
                    <strong>Cerita Selanjutnya:</strong> ${timeline.next.title}
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
};
