<?php
require_once __DIR__ . '/../BaseModel.php';
require_once __DIR__ . '/TrainerData.php';

class TrainerAppointmentsModel extends BaseModel {
    
    /**
     * Get all pending training requests for the trainer
     */
    public function getPendingRequests($trainerId) {
        $data = TrainerData::getAllSessions();
        return $data['pending'];
    }
    
    /**
     * Get all confirmed/active training sessions for the trainer
     */
    public function getConfirmedSessions($trainerId) {
        $data = TrainerData::getAllSessions();
        return $data['confirmed'];
    }
    
    /**
     * Get all completed training sessions for the trainer
     */
    public function getCompletedSessions($trainerId) {
        $data = TrainerData::getAllSessions();
        return $data['completed'];
    }

    
    /**
     * Accept a training request
     */
    public function acceptRequest($requestId, $trainerId) {
        // Mock implementation - replace with actual database update
        return [
            'success' => true,
            'message' => 'Training request accepted successfully'
        ];
    }
    
    /**
     * Decline a training request
     */
    public function declineRequest($requestId, $trainerId, $reason = '') {
        // Mock implementation - replace with actual database update
        return [
            'success' => true,
            'message' => 'Training request declined'
        ];
    }
    
    /**
     * Complete a training session and save notes
     */
    public function completeSession($sessionId, $trainerId, $notes, $nextSessionDate = null, $nextSessionTime = null, $nextSessionGoals = '') {
        // Mock implementation - replace with actual database update
        return [
            'success' => true,
            'message' => 'Training session completed and notes saved successfully'
        ];
    }
    
    /**
     * Mark entire training program as completed
     */
    public function markProgramComplete($sessionId, $trainerId, $finalNotes) {
        // Mock implementation - replace with actual database update
        return [
            'success' => true,
            'message' => 'Training program marked as complete'
        ];
    }
    
    /**
     * Get session by ID
     */
    public function getSessionById($sessionId) {
        // Mock implementation - in real app, query database
        $confirmedSessions = $this->getConfirmedSessions(null);
        foreach ($confirmedSessions as $session) {
            if ($session['session_id'] == $sessionId) {
                return $session;
            }
        }
        return null;
    }
    
    /**
     * Get request by ID
     */
    public function getRequestById($requestId) {
        // Mock implementation - in real app, query database
        $pendingRequests = $this->getPendingRequests(null);
        foreach ($pendingRequests as $request) {
            if ($request['request_id'] == $requestId) {
                return $request;
            }
        }
        return null;
    }
    
    /**
     * Get session history (all previous session notes) for a training program
     */
    public function getSessionHistory($sessionId) {
        // Mock data - in real app, query database for all previous sessions
        // This would join session_notes table or similar
        return [
            [
                'session_number' => 1,
                'session_date' => '2025-09-26',
                'notes' => 'First session with Rocky. He is very energetic and enthusiastic. Started with basic sit and stay commands. He responds well to treats. Needs work on focus and attention span.',
                'goals_for_next' => 'Continue practicing sit and stay. Introduce heel command.'
            ],
            [
                'session_number' => 2,
                'session_date' => '2025-09-29',
                'notes' => 'Good progress on sit command. Rocky now sits consistently on command. Started heel training - he pulls on leash initially but improving. Practiced recall with distractions.',
                'goals_for_next' => 'Focus on heel work and loose leash walking. Practice commands with more distractions.'
            ],
            [
                'session_number' => 3,
                'session_date' => '2025-10-03',
                'notes' => 'Rocky is doing much better with leash manners. Heel command is improving. Introduced down command. He gets excited when other dogs are around - need to work on impulse control.',
                'goals_for_next' => 'Continue leash work. Practice down-stay. Start impulse control exercises.'
            ],
            [
                'session_number' => 4,
                'session_date' => '2025-10-07',
                'notes' => 'Excellent session! Rocky held down-stay for 30 seconds. Worked on impulse control with food temptations - showing good progress. Owner is also improving with consistent commands and timing.',
                'goals_for_next' => 'Increase duration of stays. Practice all commands with high-level distractions.'
            ],
            [
                'session_number' => 5,
                'session_date' => '2025-10-10',
                'notes' => 'Rocky showed great improvement on recall commands. Distraction training went well. Continue working on impulse control around food. Overall excellent progress - he is becoming very reliable with basic commands.',
                'goals_for_next' => 'Focus on advanced heel work and practicing commands with increased distractions.'
            ]
        ];
    }
}
