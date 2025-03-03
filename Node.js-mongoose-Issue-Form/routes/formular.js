"use strict";

const express = require('express');
const router = express.Router();

const {body,validationResult} = require('express-validator');
const formularModel = require('../models/formularModel');

async function renderIndex(res) {
  try {
    const issues = await formularModel.find();
    res.render('index', {issues});
  } catch(error) {
    console.log(error);
  }
}

/* GET home page. */
router.get('/', function(req, res, next) {
  renderIndex(res);
});


function renderForm(res, issue, errors = null) {
  res.render('formularForm', {
    issue,
    errors: errors ? errors.array() : null
  })
}

router.route('/new')
  .get((req,res) => res.render('formularForm'))
  .post(
    body('name','Name is required').trim().notEmpty(),
    body('adresse').trim(),
    body('telefonnummer',"Invalid data for 'Telefonnummer'").trim().isNumeric(),
    body('email',"Email is required").trim().notEmpty(),
    body('email',"Invalid data for 'Email'").isEmail(),
    async (req,res) => {
      const errors = validationResult(req);
      if (errors.isEmpty()) {
        // process form
        try {
          const issue = new formularModel(req.body);
          await issue.save();
          //res.render('formularForm');
          renderIndex(res);
        } catch(error) {
          console.log(error);
        }
      } else {
        // print form + error messages + valid data
        renderForm(res, req.body, errors);
      }
    });

    router.get('/delete/:id', async (req,res) => {
      try {
        await formularModel.deleteOne({_id: req.params.id});
        renderIndex(res);
      } catch(error) {
        console.log(error);
      }
    });

    router.route('/edit/:id')
      .get(async (req,res) => {
        try {
          const issue = await formularModel.findById({_id: req.params.id});
          renderForm(res, issue);
        } catch (error) {
          console.log(error);
        }
      })
      .post(
        body('name','Name is required').trim().notEmpty(),
        body('adresse').trim(),
        body('telefonnummer').isNumeric(),
        body('email',"Email is required").trim().notEmpty(),
        body('email',"Invalid data for 'email'").isEmail(),
        async (req,res) => {
          const errors = validationResult(req);
          if (errors.isEmpty()) {
            // process form
            try {
              if (!req.body.newsletter) req.body.newsletter = null;
              await formularModel.findOneAndUpdate(
                {_id: req.params.id},
                req.body
              );
            renderIndex(res);
          } catch(error) {
            console.log(error);
          }
        } else {
          // print form + error messages + valid data
          renderForm(res, req.body, errors);
        }
      });


module.exports = router;
