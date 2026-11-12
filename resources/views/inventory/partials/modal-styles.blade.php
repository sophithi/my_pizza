.restock-overlay {
    overflow-y: auto;
    overscroll-behavior: contain;
}

.restock-overlay .restock-modal {
    max-height: calc(100vh - 40px);
    max-height: calc(100dvh - 40px);
    overflow-y: auto;
    overscroll-behavior: contain;
}

.restock-overlay :is(button, input, select, textarea):focus-visible {
    outline: 2px solid #ea580c;
    outline-offset: 3px;
}

@media (max-height: 600px) {
    .restock-overlay {
        align-items: flex-start;
        padding: 12px;
    }

    .restock-overlay .restock-modal {
        margin: auto;
        max-height: calc(100vh - 24px);
        max-height: calc(100dvh - 24px);
    }
}
