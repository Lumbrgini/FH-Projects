"use strict";
const express = require('express');
const router = express.Router();
const { ObjectId } = require('mongodb');

const NAME_COLLECTION="notesList";

router.get("/", async (req, res) => {
    try {
        res.json(
            await req.db.collection(NAME_COLLECTION).find().toArray()
        );
    } catch (error) {
        console.log(error);
    }
});

router.post("/", async (req, res) => {
    try {
        res.json(
            await req.db.collection(NAME_COLLECTION).insertOne(req.body)
        );
    } catch (error) {
        console.log(error);
    }
});

router.delete("/:id", async (req, res) => {
    try {
        res.json(
            await req.db.collection(NAME_COLLECTION).deleteOne(
                {_id: new ObjectId(req.params.id)}
            )
        );
    } catch (error) {
        console.log(error);
    }
});

router.put("/:id", async (req, res) => {
    try {
        res.json(
            await req.db.collection(NAME_COLLECTION).updateOne(
                {_id: new ObjectId(req.params.id)},
                {$set:{name: req.body.name, surname: req.body.surname}}
            )
        );
    } catch (error) {
        console.log(error);
    }
});

module.exports = router;
