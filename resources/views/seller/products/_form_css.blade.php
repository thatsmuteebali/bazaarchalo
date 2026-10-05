<style>
    .image-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 12px;
    }

    .image-tile {
        position: relative;
        aspect-ratio: 1 / 1;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        color: #64748b;
        font-size: 13px;
        cursor: pointer;
        overflow: hidden;
        transition: border-color .2s, background .2s;
    }

    .image-tile:hover {
        border-color: #198754;
        background: #f0fdf4;
        color: #198754;
    }

    .image-tile i.bi {
        font-size: 26px;
    }

    .image-tile input[type="file"] {
        display: none;
    }

    .image-tile.filled {
        border-style: solid;
        border-color: #e2e8f0;
        cursor: default;
        background: #fff;
    }

    .image-tile img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-tile .remove-btn {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 26px;
        height: 26px;
        border: 0;
        border-radius: 50%;
        background: rgba(15, 23, 42, .75);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        cursor: pointer;
    }

    .image-tile .remove-btn:hover {
        background: #dc3545;
    }

    .image-tile .cover-badge {
        position: absolute;
        left: 6px;
        bottom: 6px;
        padding: 2px 8px;
        border-radius: 20px;
        background: #198754;
        color: #fff;
        font-size: 11px;
        font-weight: 600;
    }

    .image-hint {
        font-size: 12px;
        color: #94a3b8;
    }

    .image-error {
        font-size: 13px;
        color: #dc3545;
        margin-top: 8px;
        display: none;
    }

    .section-title {
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 14px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e2e8f0;
    }

    /* ---------- Variants ---------- */
    #variantsFieldset {
        border: 0;
        padding: 0;
        margin: 0;
        min-width: 0;
    }

    .option-row {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px;
        margin-bottom: 12px;
        background: #f8fafc;
    }

    .option-head {
        display: flex;
        gap: 8px;
        margin-bottom: 10px;
    }

    .icon-btn {
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        border-radius: 8px;
        width: 40px;
        flex-shrink: 0;
        cursor: pointer;
    }

    .icon-btn:hover {
        color: #dc3545;
        border-color: #dc3545;
    }

    .chip-box {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 8px;
        min-height: 42px;
    }

    .chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #e8f5ee;
        color: #146c43;
        border-radius: 20px;
        padding: 3px 6px 3px 12px;
        font-size: 13px;
        font-weight: 500;
    }

    .chip-x {
        border: 0;
        background: rgba(20, 108, 67, .15);
        color: #146c43;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        line-height: 1;
        font-size: 13px;
        cursor: pointer;
        padding: 0;
    }

    .chip-x:hover {
        background: #dc3545;
        color: #fff;
    }

    .chip-input {
        border: 0;
        outline: 0;
        flex: 1;
        min-width: 140px;
        font-size: 14px;
        background: transparent;
    }

    .variants-table-wrap {
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    .variants-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        min-width: 720px;
    }

    .variants-table th {
        background: #f8fafc;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: #64748b;
        padding: 10px 12px;
        white-space: nowrap;
    }

    .variants-table td {
        padding: 8px 12px;
        border-top: 1px solid #e2e8f0;
        vertical-align: middle;
    }

    .variants-table td input {
        width: 100%;
        min-width: 90px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 14px;
    }

    .variants-table td input:focus {
        outline: 0;
        border-color: #198754;
    }

    .variant-name {
        font-weight: 600;
        white-space: nowrap;
    }

    .bulk-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        margin: 14px 0 10px;
        font-size: 13px;
        color: #64748b;
    }

    .bulk-bar input {
        width: 120px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 14px;
    }

    .link-btn {
        border: 0;
        background: none;
        color: #198754;
        font-size: 13px;
        padding: 0;
        cursor: pointer;
        text-decoration: underline;
    }

    /* ---------- Edit page additions ---------- */
    .stock-static {
        display: inline-block;
        padding: 4px 12px;
        background: #f1f5f9;
        border-radius: 8px;
        font-weight: 600;
    }

    .stock-zero {
        background: #fee2e2;
        color: #b91c1c;
    }

    .move-plus {
        color: #198754;
        font-weight: 600;
    }

    .move-minus {
        color: #dc3545;
        font-weight: 600;
    }
</style>
