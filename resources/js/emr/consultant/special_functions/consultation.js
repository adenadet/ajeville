export function createEmptyConsultation(id = null) {
    return {
        id,

        complaint: '',
        history: '',

        initial_diagnosis: [],
        final_diagnosis: [],

        plan: {
            plan: '',
            non_drug: '',
            follow_up_date: null,
            follow_up_note: '',
            intent: {
                admission: false,
                referral: false,
            },
        },

        requests: {
            prescription: [],
            laboratory: [],
            radiology: [],
            physiotherapy: [],
            dialysis: {},
            admission: null,
            referral: null,
        },
    };
}
