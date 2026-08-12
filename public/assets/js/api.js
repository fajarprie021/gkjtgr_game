/**
 * API Service for Bible Adventure
 * Handles communication with PHP backend
 */

const ApiService = {
  async getEras() {
    try {
      const response = await fetch("api/eras.php");
      const result = await response.json();
      if (result.success) return result.data;
      throw new Error(result.message || "Failed to fetch eras");
    } catch (error) {
      console.error("API Error (Eras):", error);
      return [];
    }
  },

  async getStories() {
    try {
      const response = await fetch("api/stories.php");
      const result = await response.json();
      if (result.success) return result.data;
      throw new Error(result.message || "Failed to fetch stories");
    } catch (error) {
      console.error("API Error (Stories):", error);
      return [];
    }
  },

  async getStoryById(storyId) {
    try {
      const stories = await this.getStories();
      return stories.find((s) => s.id === storyId) || null;
    } catch (error) {
      console.error("API Error (Get Story by ID):", error);
      return null;
    }
  },

  async getQuestions(storyId, classGroup) {
    try {
      const response = await fetch(
        `api/questions.php?storyId=${storyId}&classGroup=${classGroup}`,
      );
      const result = await response.json();
      if (result.success) return result.data;
      throw new Error(result.message || "Failed to fetch questions");
    } catch (error) {
      console.error("API Error (Questions):", error);
      return []; // Return empty to be handled by UI
    }
  },

  async getStoryContent(storyId, classGroup) {
    try {
      const response = await fetch(
        `api/story-content.php?id=${storyId}&class=${classGroup}`,
      );
      const result = await response.json();
      if (result.success) return result.data.content;
      throw new Error(result.message || "Failed to fetch story content");
    } catch (error) {
      console.error("API Error (Story Content):", error);
      return null;
    }
  },

  async validateAnswer(questionId, answer) {
    try {
      const response = await fetch("api/answer.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          question_id: questionId,
          answer: answer,
        }),
      });
      const result = await response.json();
      if (result.success) return result;
      throw new Error(result.message || "Failed to validate answer");
    } catch (error) {
      console.error("API Error (Validate Answer):", error);
      throw error;
    }
  },

  async logAnalyticsEvent(payload) {
    try {
      await fetch("api/analytics/event.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
    } catch (error) {
      console.warn("Analytics event failed (best effort):", error);
    }
  },
};
